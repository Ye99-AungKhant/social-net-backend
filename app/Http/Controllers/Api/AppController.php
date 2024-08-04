<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Friendship;
use App\Models\Notification;
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

        $chatNoti = Chat::where('receiver_id', $auth->id)->where('read', false)->get();
        $noti = $auth->notificationsFromPosts();

        return response()->json([
            'success' => true,
            'auth' => $auth,
            'friendRequestNoti' => $friendRequestNoti,
            'friendList' => UserResource::collection($friendLists),
            'chatNoti' => $chatNoti,
            'notification' => NotificationResource::collection($noti)
        ]);
    }
}
