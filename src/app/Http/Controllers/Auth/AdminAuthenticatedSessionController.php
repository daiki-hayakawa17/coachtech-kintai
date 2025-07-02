<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['role'] = 'admin';

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || $user->role !== 'admin') {
            return redirect('/admin/login')->withErrors([
                'email' => 'ログイン情報が登録されていません'
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('/admin/list');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。'
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
