<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getPost($id)
    {
        $post = Post::with('like', 'media', 'user')->withCount('like', 'comment')->where('user_id', $id)->simplePaginate(10);
        return PostResource::collection($post)->additional([
            'success' => true,
        ], 200);
    }

    public function profileData($id)
    {
        $user = User::find($id);
        $friendLists = $user->getFriendsList($id);

        // $friendsLists = [];
        // foreach ($friendsList as $list) {
        //     $friendsLists[] = User::where('id', $list)->first();
        // }
        // return $friendsList;

        return response()->json([
            'profileData' => new UserResource($user),
            'friendList' => UserResource::collection($friendLists)
        ], 200);
    }
}
