<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function login()
    {
        $data = [
            'title' => 'Đăng nhập hệ thống'
        ];

        return view('admin.login', $data);
    }

    public function submitLogin(Request $request)
    {
        $request->validate([
            'login' => 'required', // login có thể là email hoặc username
            'password' => 'required',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $login_type => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            if (auth()->user()->role == "admin") {
                return redirect()->intended('/admin');
            } else {
                Auth::logout();
                return redirect()->route('admin.login')->withErrors(['error' => 'Bạn không có quyền truy cập trang quản trị.']);
            }
        }

        return redirect()->route('admin.login')->withErrors(['error' => 'Đăng nhập không thành công. Vui lòng kiểm tra lại thông tin.']);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|min:4|confirmed',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.max' => 'Tên đăng nhập không được vượt quá 50 ký tự.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            
            'current_password.required_with' => 'Vui lòng nhập mật khẩu hiện tại.',
            
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 4 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
            }
            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
