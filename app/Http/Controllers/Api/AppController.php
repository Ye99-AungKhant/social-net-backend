<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Friendship;
use App\Models\Media;
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

    public function photos()
    {
        $photos = Media::orderBy('updated_at', 'DESC')->simplePaginate(10);
        return response()->json([
            'success' => true,
            'data' => MediaResource::collection($photos)
        ], 200);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $posts = Post::with('user', 'like', 'media')->withCount('like', 'comment')->where('content', 'LIKE', "%{$searchTerm}%")->get();
        $users = User::where('name', 'LIKE', "%{$searchTerm}%")->get();

        return response()->json([
            'posts' => PostResource::collection($posts),
            'users' => UserResource::collection($users)
        ], 200);
    }
}
