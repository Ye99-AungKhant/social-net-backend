<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'like')->withCount('like')->get();

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
        ], 200);
    }
}
