<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'phone' => [
                'required',
                'string',
                'max:20'
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8'
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],

            // IMPORTANT:
            // Customers can only create customer accounts.
            'role' => 'customer',
            'status' => 'active',
        ]);

        $token = $user
            ->createToken('customer-mobile')
            ->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email'
            ],

            'password' => [
                'required'
            ],
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Invalid email or password.'
                ],
            ]);
        }

        if ($user->role !== 'customer') {
            return response()->json([
                'message' =>
                    'This login is only for customers.'
            ], 403);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' =>
                    'Your account is inactive.'
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user
            ->createToken('customer-mobile')
            ->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }
}