<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\FriendResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use App\Models\UserAbout;
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
        $authId = auth()->user()->id;
        $friendLists = $user->getFriendsList($id, 'Accepted');

        $waitingFriendLists = User::with('friendsOf.addingUser')->where('id', $authId)->first();
        $aboutus = UserAbout::where('user_id', $id)->get();

        return response()->json([
            'profileData' => new UserResource($user),
            'friendList' => $friendLists,
            'waitingfriendList' => FriendResource::collection($waitingFriendLists->friendsOf),
            'aboutus' => AboutUsResource::collection($aboutus)
        ], 200);
    }

    public function createUserAbout(Request $request)
    {
        $aboutUsData = $request->data;
        $authId = auth()->user()->id;
        $rsdata = [];
        foreach ($aboutUsData as $data) {
            if ($data['textValue']) {
                $rsdata[] = UserAbout::create([
                    'user_id' => $authId,
                    'icon' => $data['selectValue'],
                    'content' => $data['textValue'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => AboutUsResource::collection($rsdata)
        ], 200);
    }

    public function deleteUserAbout($id)
    {
        UserAbout::where('id', $id)->delete();
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function addUserBio(Request $request)
    {
        $authId = auth()->user()->id;
        User::where('id', $authId)->update(['bio' => $request->bio]);
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function updateUserProfile(Request $request)
    {
        $authUser = auth()->user();
        $profile = $request->profile;
        $name = $request->name;
        $user = User::findOrFail($authUser->id);
        if ($profile) {
            $user->profile = $profile;
        }
        if ($name != $authUser->name) {
            $user->name = $name;
        }
        $user->save();
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ], 200);
    }
}
