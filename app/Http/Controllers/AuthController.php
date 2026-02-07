<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLogin() {
        return view('auth.login');
    }

    // تنفيذ تسجيل الدخول
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // التوجيه بناءً على الصلاحية
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            }
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة',
        ]);
    }

    // عرض صفحة التسجيل
    public function showRegister() {
        return view('auth.register');
    }

    // تنفيذ التسجيل
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'تم إنشاء الحساب بنجاح');
    }

    // تسجيل الخروج
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /* --- دوال الأدمن --- */

    public function getAllUsers() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function registerByAdmin(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,user', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function updateRole(Request $request, $id) {
        $request->validate([
            'role' => 'required|in:admin,staff,user'
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'تم تحديث الصلاحية بنجاح');
    }

    public function destroy($id)
{
    // تأمين: م ينفعش الآدمن يمسح نفسه!
    if (auth()->id() == $id) {
        return back()->with('error', 'لا يمكنك حذف حسابك الشخصي!');
    }

    $user = \App\Models\User::findOrFail($id);
    $user->delete();

    return back()->with('success', 'تم حذف المستخدم بنجاح.');
}
}