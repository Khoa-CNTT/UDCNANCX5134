<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
