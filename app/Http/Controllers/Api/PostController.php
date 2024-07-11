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
        $liked = new Like();
        $liked->post_id = $request->postId;
        $liked->user_id = $authUserId;
        $liked->save();
        return response()->json([
            'success' => true,
            'data' => $liked
        ], 200);
    }
}
