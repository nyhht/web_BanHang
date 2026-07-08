<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();
        return view('admin.pages.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        if ($request->type === "profile") {
            $request->validate([
                "name" => "required|string|min:3",
                "phone" => 'nullable|string|max:15',
                "address" => "required|string",
            ]);

            $user->update([
                "name" => $request->name,
                "phone_number" => $request->phone,
                "address" => $request->address,
            ]);

            return response()->json(["status" => true, "message" => "Cập nhật thông tin thành công!"]);
        } elseif ($request->type === "password") {
            $request->validate([
                "current_password" => "required",
                "new_password" => "required|min:6",
                "confirm_password" => "required|same:new_password",
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(["status" => false, "message" => "Mật khẩu hiện tại không đúng!"]);
            }

            $user->update(["password" => Hash::make($request->new_password)]);
            return response()->json(["status" => true, "message" => "Đổi mật khẩu thành công!"]);
        } elseif ($request->type === "avatar" && $request->hasFile("avatar")) {
            //Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file("avatar");
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $avatarPath->getClientOriginalExtension();
            $avatarPath = $avatarPath->storeAs('uploads/users', $fileName, 'public');

            $user->avatar = $avatarPath;
            $user->save();

            return response()->json([
                "status" => true,
                "message" => "Cập nhật ảnh đại diện thành công!",
                "avatar_url" => asset("storage/" . $avatarPath),
            ]);
        }

        return response()->json(["status" => false, "message" => "Yêu cầu không hợp lệ!"]);
    }
}