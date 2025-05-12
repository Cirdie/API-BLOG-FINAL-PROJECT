<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ✅ Register new user
   public function register(Request $request)
{
    $request->validate([
        'name'                  => 'required|string|max:255',
        'email'                 => 'required|email|unique:users,email',
        'password'              => 'required|string|min:6|confirmed',
        'gender'                => 'nullable|string|max:20',
        'image'                 => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $imageName = 'default.png'; // fallback

    if ($request->hasFile('image')) {
        $imageName = md5($request->file('image')->getClientOriginalName()) . ".jpg";
        $request->file('image')->storeAs('public/', $imageName);
    }

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => bcrypt($request->password),
        'role'     => 'user',
        'gender'   => $request->gender,
        'image'    => $imageName,
    ]);

    return response()->json([
        'message' => 'Registration successful. Please login.',
        'email'   => $user->email
    ], 201);
}


    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.']
        ]);
    }

    // 🔁 Revoke existing tokens before issuing a new one
    $user->tokens()->delete();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful.',
        'email'   => $user->email,
        'token'   => $token
    ]);
}



    // ✅ Logout: revoke current token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
