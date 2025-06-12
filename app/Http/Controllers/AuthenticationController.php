<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Mail\Auth\OtpRegisterEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        return view('pages.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            // 'g-recaptcha-response' => 'recaptcha',
        ]);

        if (auth()->attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        } else {
            return back()->with('error', 'Email atau password salah');
        }
    }

    public function register(Request $request)
    {
        return view('pages.auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'password_verify' => 'required|same:password',
            'otp_code' => 'required|digits:6',
            'g-recaptcha-response' => 'recaptcha',
        ]);

        try{
            $otp_service = new OtpService();
            if ($otp_service->validate($request->email, 'register', $request->otp_code, $request->ip())) {
                $user = User::create([
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                $user->assignRole('operator');

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'redirect' => route('login.index'),
                    ],
                    'message' => 'Registrasi berhasil',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP tidak valid',
                ]);
            }
        }catch(\Exception $e){
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silahkan coba lagi nanti'
            ]);
        }
    }

    public function generateOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // TODO : set rate limiter
            $otp = OtpService::generate($request->email, 'register', $request->ip());
            Mail::to($request->email)->queue(new OtpRegisterEmail($otp::$plain_token));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'resend_after' => 60*5,
                'last_otp_at' => isset($otp) ? $otp::$expired_at->setTimezone('GMT+8')->format('Y-m-d H:i:s') : now()->addDay()->setTimezone('GMT+8')->format('Y-m-d H:i:s'),
            ],
            'message' => 'Kode OTP berhasil dikirim ke email',
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('login.index');
    }
}
