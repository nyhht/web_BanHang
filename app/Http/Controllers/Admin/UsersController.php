<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\LeadScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    private const USER_STATUSES = ['pending', 'active', 'banned', 'deleted'];

    public function __construct(private LeadScoringService $leadScoringService)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleId = $request->input('role_id');

        $usersQuery = User::with('role')->latest();
        $roles = Role::orderBy('name')->get();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        if ($roleId && $roles->contains('id', (int) $roleId)) {
            $usersQuery->where('role_id', $roleId);
        }

        $users = $usersQuery->paginate(9)->appends($request->query());
        $statuses = self::USER_STATUSES;
        $leadProfiles = $this->leadScoringService->profilesForUsers(collect($users->items()));
        $vipCustomers = $this->leadScoringService->topCustomers();

        return view('admin.pages.users', compact(
            'users',
            'search',
            'roleId',
            'roles',
            'statuses',
            'leadProfiles',
            'vipCustomers'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['required', Rule::in(self::USER_STATUSES)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $data['role_id'],
            'status' => $data['status'],
            'avatar' => $this->storeAvatar($request),
            'activation_token' => null,
        ]);

        toastr()->success('Thêm người dùng thành công.');

        return redirect()->route('admin.users.index');
    }

    public function update(Request $request)
    {
        $user = User::with('role')->findOrFail($request->input('user_id'));

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['required', Rule::in(self::USER_STATUSES)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($this->isCurrentAdmin($user)) {
            $data['role_id'] = $user->role_id;
            $data['status'] = $user->status;
        }

        if (!$this->keepsAtLeastOneActiveAdmin($user, (int) $data['role_id'], $data['status'])) {
            toastr()->error('Không thể làm mất admin đang hoạt động cuối cùng.');

            return redirect()->route('admin.users.index');
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $data['role_id'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $this->storeAvatar($request, $user);
        }

        $user->update($updateData);

        toastr()->success('Cập nhật người dùng thành công.');

        return redirect()->route('admin.users.index');
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::with('role')->findOrFail($data['user_id']);

        if ($this->isCurrentAdmin($user)) {
            toastr()->error('Bạn không thể xóa tài khoản đang đăng nhập.');

            return redirect()->route('admin.users.index');
        }

        if (!$this->keepsAtLeastOneActiveAdmin($user, null, 'deleted')) {
            toastr()->error('Không thể xóa admin đang hoạt động cuối cùng.');

            return redirect()->route('admin.users.index');
        }

        $user->update(['status' => 'deleted']);

        toastr()->success('Xóa người dùng thành công.');

        return redirect()->route('admin.users.index');
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', Rule::in(self::USER_STATUSES)],
        ]);

        $user = User::with('role')->find($data['user_id']);

        if ($this->isCurrentAdmin($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể đổi trạng thái tài khoản đang đăng nhập.',
            ]);
        }

        if (!$this->keepsAtLeastOneActiveAdmin($user, $user->role_id, $data['status'])) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể làm mất admin đang hoạt động cuối cùng.',
            ]);
        }

        $user->status = $data['status'];
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Trạng thái người dùng đã được cập nhật.',
        ]);
    }

    private function storeAvatar(Request $request, ?User $user = null): ?string
    {
        if (!$request->hasFile('avatar')) {
            return null;
        }

        if (
            $user
            && $user->avatar
            && $user->avatar !== 'uploads/users/default-avatar.png'
            && Storage::disk('public')->exists($user->avatar)
        ) {
            Storage::disk('public')->delete($user->avatar);
        }

        $avatar = $request->file('avatar');
        $fileName = now()->timestamp . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

        return $avatar->storeAs('uploads/users', $fileName, 'public');
    }

    private function isCurrentAdmin(?User $user): bool
    {
        return $user && $user->id === Auth::guard('admin')->id();
    }

    private function keepsAtLeastOneActiveAdmin(User $user, ?int $newRoleId, string $newStatus): bool
    {
        if (!$user->role || $user->role->name !== 'admin') {
            return true;
        }

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            return true;
        }

        if ($newRoleId === $adminRole->id && $newStatus === 'active') {
            return true;
        }

        return User::where('role_id', $adminRole->id)
            ->where('status', 'active')
            ->where('id', '!=', $user->id)
            ->exists();
    }
}
