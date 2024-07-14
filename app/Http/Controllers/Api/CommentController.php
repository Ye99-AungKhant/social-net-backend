<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('user')->where('post_id', $request->postId)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments)
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
            'data' => [new CommentResource($comment)]
        ], 200);
    }

    public function delete($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'success' => true,
            'data' => $comment->post_id
        ], 200);
    }

    public function edit(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->content = $request->content;
        $comment->save();
        return response()->json([
            'success' => true,
            'data' => new CommentResource($comment)
        ], 200);
    }
}
