<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserPostController extends Controller
{
    // ✅ Create a new post
    public function create(Request $request)
{
    $user = $request->user();

    $today = Carbon::now()->toDateString();
    $dailyPostCount = Post::where('User_Id', $user->user_id)
                          ->whereDate('created_at', $today)
                          ->count();

    if ($dailyPostCount >= 5) {
        return response()->json(['message' => 'Daily post limit reached.'], 429);
    }

    // ✅ Include topic_id in validation
    $validator = Validator::make($request->all(), [
        'title'     => 'required|string|max:255',
        'content'   => 'required|string|min:10',
        'topic_id'  => 'required|exists:topics,Topic_Id',
        'postImage' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $imageName = null;
    if ($request->hasFile('postImage')) {
        $imageName = md5($request->file('postImage')->getClientOriginalName()) . ".jpg";
        $request->file('postImage')->storeAs('public/', $imageName);
    }

    // ✅ Add Topic_Id here
    $post = Post::create([
        'User_Id'     => $user->user_id,
        'Topic_Id'    => $request->topic_id,
        'title'       => $request->title,
        'content'     => $request->content,
        'image'       => $imageName,
        'is_approved' => 0,
        'save_count'  => 0,
    ]);

    return response()->json([
        'message' => 'Post submitted for approval.',
        'post'    => $post
    ], 201);
}


    public function approvedPosts()
{
    $posts = Post::where('is_approved', 1)
                 ->with(['user', 'topic']) // optional: include user and topic details
                 ->orderBy('created_at', 'desc')
                 ->get();

    return response()->json([
        'message' => 'Approved posts retrieved successfully.',
        'posts' => $posts
    ]);
}


    // ✅ List authenticated user's own posts
    public function myPosts(Request $request)
    {
        $user = $request->user();

        $posts = Post::where('User_Id', $user->user_id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json([
            'posts' => $posts
        ]);
    }

    // ✅ Optional: View single post by ID (only if owner)
    public function view(Request $request, $id)
    {
        $post = Post::where('Post_Id', $id)
                    ->where('User_Id', $request->user()->user_id)
                    ->first();

        if (! $post) {
            return response()->json(['message' => 'Post not found or access denied.'], 404);
        }

        return response()->json(['post' => $post]);
    }

    public function edit(Request $request, $Post_Id)
{
    $user = $request->user();
    $post = Post::where('Post_Id', $Post_Id)
                ->where('User_Id', $user->user_id)
                ->first();

    if (! $post) {
        return response()->json(['message' => 'Post not found or unauthorized.'], 404);
    }

    // Validate update input
    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|string|max:255',
        'content' => 'sometimes|string|min:10',
        'postImage' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update fields
    if ($request->has('title')) $post->title = $request->title;
    if ($request->has('content')) $post->content = $request->content;

    // Handle image replacement
    if ($request->hasFile('postImage')) {
        if ($post->image) {
            Storage::delete('public/' . $post->image);
        }

        $imageName = md5($request->file('postImage')->getClientOriginalName()) . ".jpg";
        $request->file('postImage')->storeAs('public/', $imageName);
        $post->image = $imageName;
    }

    $post->is_approved = 0; // Reset approval
    $post->save();

    return response()->json(['message' => 'Post updated and sent for re-approval.', 'post' => $post]);
}


public function delete(Request $request, $Post_Id)
{
    $user = $request->user();
    $post = Post::where('Post_Id', $Post_Id)
                ->where('User_Id', $user->user_id)
                ->first();

    if (! $post) {
        return response()->json(['message' => 'Post not found or unauthorized.'], 404);
    }

    // Delete image if it exists
    if ($post->image) {
        Storage::delete('public/' . $post->image);
    }

    $post->delete();

    return response()->json(['message' => 'Post deleted successfully.']);
}


}
