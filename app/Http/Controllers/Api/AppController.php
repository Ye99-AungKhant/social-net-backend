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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    public function index()
    {
        $auth = User::select('id', 'name', 'profile')->where('id', Auth::id())->first();

        $friendRequestNoti = Friendship::where('adding_user_id', $auth->id)->where('status', 'Requested')->count();

        $friendLists = $auth->getFriendsList($auth->id, 'Accepted');

        $chatNoti = Chat::where('receiver_id', $auth->id)->where('read', false)->get();
        $noti = $auth->notificationsFromPosts();

        $messageUsers = DB::table('chats')
            ->where('sender_id', $auth->id)
            ->orWhere('receiver_id', $auth->id)
            ->where(function ($query) use ($auth) {
                $query->where('sender_id', '!=', $auth->id)
                    ->orWhere('receiver_id', '!=', $auth->id);
            })
            ->select('sender_id', 'receiver_id')
            ->get();

        // Extract unique user IDs, excluding the authenticated user
        $userIds = $messageUsers->map(function ($chat) use ($auth) {
            return $chat->sender_id == $auth->id ? $chat->receiver_id : $chat->sender_id;
        })->unique();

        $chatUsers = User::whereIn('id', $userIds)->get();

        // Combine both friend list and chat users, ensuring no duplicates
        $chatUser = $friendLists->merge($chatUsers)->unique('id');

        return response()->json([
            'success' => true,
            'auth' => $auth,
            'friendRequestNoti' => $friendRequestNoti,
            'friendList' => UserResource::collection($friendLists),
            'chatUserList' => UserResource::collection($chatUser),
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

    public function updateLastOnline(Request $request)
    {
        return $request;
        $userId = $request->userId;
        $date = Carbon::now();
        User::where('id', $userId)->update(['lastOnline', $date]);
    }
}
