<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    // 🟢 User creates feedback
   public function store(Request $request)
{
    if ($request->user()->role !== 'user') {
        return response()->json(['message' => 'Only users can send feedback.'], 403);
    }

    $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    $feedback = Feedback::create([
        'user_id' => $request->user()->user_id,
        'message' => $request->message,
    ]);

    return response()->json([
        'message' => 'Thank you for your feedback!',
        'feedback' => $feedback
    ], 201);
}


    // 🔐 Admin views all feedback
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Admins only.'], 403);
        }

        $feedback = Feedback::with('user')->latest()->get();

        return response()->json($feedback);
    }
}
