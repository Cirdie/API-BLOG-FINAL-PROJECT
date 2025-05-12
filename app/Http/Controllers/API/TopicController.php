<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;

class TopicController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admins only.'
            ], 403);
        }

        // ✅ Validate input
        $request->validate([
            'name' => 'required|string|max:255|unique:topics,name',
        ]);

        // ✅ Create topic
        $topic = Topic::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Topic created successfully.',
            'topic' => $topic
        ], 201);
    }

    public function index()
    {
        return response()->json(Topic::all());
    }
}
