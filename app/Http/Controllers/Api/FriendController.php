<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index()
    {
        $authId = auth()->user()->id;
        $requestedFriendLists = User::with(['friendsAdded' => function ($query) {
            $query->where('status', 'Requested');
        }, 'friendsAdded.addedUser'])->where('id', $authId)->first();
        return response()->json([
            'success' => true,
            'data' => FriendResource::collection($requestedFriendLists->friendsAdded)
        ], 200);
    }

    public function friendRequest($id)
    {
        $authId = auth()->user()->id;
        $addFriend = Friendship::create([
            'adding_user_id' => $id,
            'added_user_id' => $authId,
            'status' => 'Requested'
        ]);
        return $addFriend;
    }

    public function friendRequestAccept(Request $request)
    {
        Friendship::where('id', $request->id)->update(['status' => 'Accepted']);
        return response()->json(['success' => true], 200);
    }

    public function friendRequestDecline(Request $request)
    {
        Friendship::where('id', $request->id)->update(['status' => 'Declined']);
        return response()->json(['success' => true], 200);
    }

    public function unfriend($id)
    {
        $authId = auth()->user()->id;
        $friend = Friendship::where('added_user_id', $authId)->orWhere('adding_user_id', $authId)
            ->where('added_user_id', $id)->orWhere('adding_user_id', $id)->first();
        $friend->delete();
        return response()->json([
            'success' => true,
        ], 200);
    }
}
