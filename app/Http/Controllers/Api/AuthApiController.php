<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'pengguna',
            'status'   => 'aktif',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user'  => $this->userResource($user),
                'token' => $token,
                'token_type' => 'bearer',
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token.',
            ], 500);
        }

        $user = auth()->user();

        if ($user->status === 'nonaktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'       => $this->userResource($user),
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return response()->json([
            'success' => true,
            'data'    => ['token' => $token, 'token_type' => 'bearer'],
        ]);
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->userResource(auth()->user()),
        ]);
    }

    private function userResource(User $user): array
    {
        return [
            'id'     => $user->id,
            'nama'   => $user->nama,
            'email'  => $user->email,
            'role'   => $user->role,
            'avatar' => $user->avatar,
            'status' => $user->status,
        ];
    }
}
