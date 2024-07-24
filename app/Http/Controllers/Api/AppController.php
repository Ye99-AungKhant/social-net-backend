<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function index()
    {
        $auth = User::select('id', 'name', 'profile')->where('id', Auth::id())->first();

        $friendRequestNoti = Friendship::where('adding_user_id', $auth->id)->where('status', 'Requested')->count();

        $friendLists = $auth->getFriendsList($auth->id, 'Accepted');

        return response()->json([
            'success' => true,
            'auth' => $auth,
            'friendRequestNoti' => $friendRequestNoti,
            'friendList' => UserResource::collection($friendLists),
        ]);
    }
}
