<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $post = new Post();
        $post->user_id = Auth::user()->id;
        $post->content = $request->content;
        $post->status = $request->status;
        $post->save();

        return response()->json([
            'success' => true,
            'data' => [new PostResource($post)]
        ], 200);
    }

    public function like(Request $request)
    {
        $authUserId = auth()->user()->id;
        $postId = $request->postId;
        $like = new Like();
        $liked = $like->where('post_id', $postId)->where('user_id', $authUserId)->first();
        if (!$liked) {
            $like->post_id = $postId;
            $like->user_id = $authUserId;
            $like->save();
            return response()->json([
                'liked' => true,
                'data' => $like
            ], 200);
        }
        $liked->delete();
        return response()->json([
            'unliked' => true,
            'data' => ['auth_id' => $authUserId, 'post_id' => $postId]
        ], 200);
    }
}
