<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'like')->withCount('like')->get();
        $auth = User::select('id', 'name')->where('id', Auth::id())->first();

        return response()->json([
            'success' => true,
            'data' => PostResource::collection($posts),
            'auth' => $auth
        ], 200);
    }
}
