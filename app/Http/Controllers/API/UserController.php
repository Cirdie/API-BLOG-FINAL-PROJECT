<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // 🟢 Get authenticated user's profile
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    // 🟢 Get posts made by the logged-in user
    public function userPosts(Request $request)
    {
        $posts = Post::where('user_id', $request->user()->user_id)->latest()->get();

        return response()->json([
            'posts' => $posts
        ]);
    }

        // 📝 Update profile
    public function editProfile(Request $request)
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'name'   => 'nullable|string|max:255',
        'email'  => 'nullable|email|unique:users,email,' . $user->user_id . ',user_id',
        'gender' => 'nullable|string|in:Male,Female,Other',
        'image'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Only update if filled
    if ($request->filled('name'))   $user->name = $request->name;
    if ($request->filled('email'))  $user->email = $request->email;
    if ($request->filled('gender')) $user->gender = $request->gender;

    if ($request->hasFile('image')) {
        if ($user->image) {
            Storage::delete('public/' . $user->image);
        }

        $imageName = md5($request->file('image')->getClientOriginalName()) . ".jpg";
        $request->file('image')->storeAs('public/', $imageName);
        $user->image = $imageName;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully.',
        'user' => $user
    ]);
}

    // 🔐 Logout the current device/session
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function listAll()
    {
        $users = User::select('user_id', 'name', 'email', 'role', 'gender', 'image')
                     ->orderBy('name', 'desc')
                     ->get();

        return response()->json([
            'users' => $users
        ]);
    }

    public function getUsersByRole($role = null)
    {
        $query = User::select('user_id', 'name', 'email', 'role', 'gender', 'image');

        if ($role === 'admin' || $role === 'users') {
            $query->where('role', $role === 'users' ? 'user' : 'admin');
        }

        $users = $query->orderBy('name')->get();

        return response()->json([
            'users' => $users
        ]);
    }

    public function promoteToAdmin(Request $request, $user_id)
{
    $authUser = $request->user();

    // ✅ Only admins can promote others
    if ($authUser->role !== 'admin') {
        return response()->json([
            'message' => 'Access denied. Only admins can promote users.'
        ], 403);
    }

    $user = User::find($user_id);

    if (! $user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    if ($user->role === 'admin') {
        return response()->json([
            'message' => 'This user is already an admin.'
        ], 400);
    }

    $user->role = 'admin';
    $user->save();

    return response()->json([
        'message' => 'User has been promoted to admin.',
        'user'    => [
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role
        ]
    ]);
}

public function demoteToUser(Request $request, $user_id)
{
    $authUser = $request->user();

    // ✅ Only admins can demote
    if ($authUser->role !== 'admin') {
        return response()->json([
            'message' => 'Access denied. Only admins can demote users.'
        ], 403);
    }

    // ❌ Prevent self-demotion
    if ($authUser->user_id == $user_id) {
        return response()->json([
            'message' => 'You cannot demote yourself.'
        ], 403);
    }

    $user = User::find($user_id);

    if (! $user) {
        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    if ($user->role === 'user') {
        return response()->json([
            'message' => 'This user is already a regular user.'
        ], 400);
    }

    $user->role = 'user';
    $user->save();

    return response()->json([
        'message' => 'User has been demoted to user.',
        'user'    => [
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role
        ]
    ]);
}



}
