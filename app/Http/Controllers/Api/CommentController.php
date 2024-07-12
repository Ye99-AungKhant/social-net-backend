<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $comments
        ], 200);
    }

    public function create(Request $request)
    {
        $comment = new Comment();
        $comment->post_id = $request->postId;
        $comment->user_id = $request->userId;
        $comment->content = $request->content;
        $comment->save();
        return response()->json([
            'success' => true,
            'data' => [$comment]
        ], 200);
    }
}
