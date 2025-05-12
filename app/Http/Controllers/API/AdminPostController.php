<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminPostController extends Controller
{

    public function create(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Access denied. Admins only.'], 403);
    }

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

    $post = Post::create([
        'User_Id'     => $user->user_id,
        'Topic_Id'    => $request->topic_id,
        'title'       => $request->title,
        'content'     => $request->content,
        'image'       => $imageName,
        'is_approved' => 1, // ✅ immediately approved
        'save_count'  => 0,
    ]);

    $post->load('topic');

    return response()->json([
        'message' => 'Post created and approved immediately.',
        'post'    => $post
    ], 201);
}


    // ✅ Approve a post
    public function approve($Post_Id)
    {
        $post = Post::find($Post_Id);

        if (! $post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $post->is_approved = 1;
        $post->save();

        return response()->json(['message' => 'Post approved successfully.', 'post' => $post]);
    }

    // ✅ Delete any post
    public function delete($Post_Id)
    {
        $post = Post::find($Post_Id);

        if (! $post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        if ($post->image) {
            Storage::delete('public/' . $post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    // ✅ Filter: Approved Posts
    public function approved()
    {
        $posts = Post::where('is_approved', 1)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json(['posts' => $posts]);
    }

    // ✅ Filter: Pending Posts
    public function pending()
    {
        $posts = Post::where('is_approved', 0)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json(['posts' => $posts]);
    }

    // ✅ All Posts (approved + pending)
    public function all()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();

        return response()->json(['posts' => $posts]);
    }

    public function filterByTopic($Topic_Id)
{
    $posts = Post::where('Topic_Id', $Topic_Id)
                ->with(['user', 'topic'])
                ->orderBy('created_at', 'desc')
                ->get();

    if ($posts->isEmpty()) {
        return response()->json(['message' => 'No posts found for this topic.'], 404);
    }

    return response()->json([
        'message' => 'Posts under selected topic.',
        'posts' => $posts
    ]);
}


public function topSavedPost()
{
    $topPost = Post::orderByDesc('save_count')->first();

    return response()->json([
        'top_post' => $topPost
    ]);
}



}
