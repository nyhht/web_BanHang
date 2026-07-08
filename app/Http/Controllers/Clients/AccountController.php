<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', Auth::id())->get();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        // dd($orders);
        return view('clients.pages.account', compact('user', 'addresses', 'orders'));
    }

    //Update information
    public function update(Request $request)
    {
        $request->validate([
            'ltn__name' => 'required|string|max:255',
            'ltn__phone_number' => 'nullable|string|max:15',
            'ltn__address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        //handle avatar
        if ($request->hasFile('avatar')) {
            //Delete old photo if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $file = $request->file('avatar');
            //Create new name with timestamp
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalExtension();

            //Save image to folder storage/app/public/uploads/users/tenfile.jpg
            $avatarPath = $file->storeAs('uploads/users', $filename, 'public');
            $user->avatar = $avatarPath;
        }


        $user->name = $request->input('ltn__name');
        $user->phone_number = $request->input('ltn__phone_number');
        $user->address = $request->input('ltn__address');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhập thông tin thành công!',
            'avatar' => asset('storage/' . $user->avatar)
        ]);

    }

    //Change password
    public function changePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_new_password' => 'required|same:new_password',
            ],
            [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'new_password.required' => 'Mật khẩu mới không được để trống.',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'confirm_new_password.required' => 'Vui lòng nhập lại mật khẩu mới.',
                'confirm_new_password.same' => 'Mật khẩu nhập lại không khớp.',
            ]
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();


        //Check if current password incorrect
        if(!Hash::check($request->current_password, $user->password))
        {
            return response()->json(['errors' => ['current_password' => ['Mật khẩu hiện tại không đông đúng!']]], 422);
        }

        //Update new password
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }

    public function addAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'google_place_id' => 'nullable|string|max:255',
            'formatted_address' => 'nullable|string|max:255',
        ]);

        //if the new address is set as default, update the orther addresses default = 0
        if($request->has('default'))
        {
            ShippingAddress::where('user_id', Auth::id())->update(['default' => 0]);
        }

        ShippingAddress::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'google_place_id' => $request->google_place_id,
            'formatted_address' => $request->formatted_address,
            'default' => $request->has('default') ? 1 : 0
        ]);

        return back()->with('success', 'Địa chỉ đã được thêm!');
    }

    public function updatePrimaryAddress(int $id)
    {
        //Find address 
        $address = ShippingAddress::where('id',$id)->where('user_id', Auth::id())->firstOrFail();

        //Set all address this user default = 0
        ShippingAddress::where('user_id', Auth::id())->update(['default'=> 0]);

        //Update address seleted => default = 1
        $address->update(['default'=> 1]);

        toastr()->success('Địa chỉ mặc định đã được cập nhật!');
        return back();
    }

    public function deleteAddress(int $id)
    {
        ShippingAddress::where('id',$id)->where('user_id', Auth::id())->delete();
        toastr()->success('Địa chỉ đã được xóa!');
        return back();
    }
}
