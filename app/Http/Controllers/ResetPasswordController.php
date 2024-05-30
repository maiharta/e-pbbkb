<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.auth.forgot-password');
    }

    public function request(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        // return $status === Password::RESET_LINK_SENT
        //     ? back()->with('success', 'Berhasil mengirim link reset password ke email Anda.')
        //     : back()->withErrors(['email' => __($status)]);
        return back()->with('success', 'Berhasil mengirim link reset password ke email Anda.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('pages.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.index')->with('success', 'Password berhasil direset. Silahlkan login')
            : back()->withErrors(['email' => __($status)]);
    }
}
