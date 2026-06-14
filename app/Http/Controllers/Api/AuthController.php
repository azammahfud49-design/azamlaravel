<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * REGISTER USER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Register berhasil. Silakan cek email verifikasi.'
        ], 201);
    }

    /**
     * LOGIN USER
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {

            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        // cek email verifikasi
        if (!$user->hasVerifiedEmail()) {

            return response()->json([
                'success' => false,
                'message' => 'Email belum diverifikasi'
            ], 403);
        }

        // hapus token lama
        $user->tokens()->delete();

        // buat token sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * LOGOUT USER
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * DATA USER LOGIN
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * VERIFY EMAIL
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(
            (string) $hash,
            sha1($user->getEmailForVerification())
        )) {

            return response()->json([
                'success' => false,
                'message' => 'Link verifikasi tidak valid'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {

            return response()->json([
                'success' => true,
                'message' => 'Email sudah diverifikasi'
            ]);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi'
        ]);
    }

    /**
     * RESEND EMAIL VERIFICATION
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {

            return response()->json([
                'message' => 'Email sudah diverifikasi'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link verifikasi dikirim ulang'
        ]);
    }

    /**
     * FORGOT PASSWORD
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'success' => true,
                'message' => 'Link reset password dikirim'
            ])
            : response()->json([
                'success' => false,
                'message' => 'Gagal mengirim reset password'
            ], 400);
    }

    /**
     * RESET PASSWORD
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ])
            : response()->json([
                'success' => false,
                'message' => 'Reset password gagal'
            ], 400);
    }
}