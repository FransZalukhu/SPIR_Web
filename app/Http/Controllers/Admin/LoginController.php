<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth/login')->with(['message' => 'Anda harus Login untuk mengakses Dashboard!.']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password');

        $user = User::where('name', $credentials['name'])
            ->where('role', 'admin')
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'name' => 'Invalid username or password, or not an admin.',
            ]);
        }

        Auth::login($user);

        $token = $user->createToken('auth-token')->plainTextToken;
        return redirect()->route('dashboard')->with('token', $token);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('error', 'Anda sudah Logout, Silahkan Login kembali untuk Mengakses Dashboard!!');
    }
}
