<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedPost;
use App\Models\Post;

class SavedPostController extends Controller
{
    // Save a post
   public function save($postId, Request $request)
{
    $user = $request->user();

    // ✅ Check if post exists AND is approved
    $post = Post::where('Post_Id', $postId)
                ->where('is_approved', 1)
                ->first();

    if (! $post) {
        return response()->json(['message' => 'Post not found or not approved.'], 404);
    }

    // Prevent duplicate save
    $exists = SavedPost::where('User_Id', $user->user_id)
                       ->where('Post_Id', $postId)
                       ->exists();

    if ($exists) {
        return response()->json(['message' => 'Post already saved.'], 409);
    }

    // Save post
    SavedPost::create([
        'User_Id' => $user->user_id,
        'Post_Id' => $postId
    ]);

    $post->increment('save_count');

    return response()->json(['message' => 'Post saved successfully.']);
}


    // Unsave a post
    public function unsave($postId, Request $request)
    {
        $user = $request->user();

        $saved = SavedPost::where('User_Id', $user->user_id)
                          ->where('Post_Id', $postId)
                          ->first();

        if (! $saved) {
            return response()->json(['message' => 'Post not found in saved list.'], 404);
        }

        $saved->delete();
        Post::where('Post_Id', $postId)->decrement('save_count');

        return response()->json(['message' => 'Post unsaved successfully.']);
    }

    // Get all saved posts
    public function savedPosts(Request $request)
    {
        $user = $request->user();

        $savedPosts = SavedPost::with('post')
            ->where('User_Id', $user->user_id)
            ->latest()
            ->get();

        return response()->json(['saved_posts' => $savedPosts]);


    }


}
