<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($id)
    {
        $authId = auth()->user()->id;
        $chat = Chat::where(function ($query) use ($authId, $id) {
            $query->where('sender_id', $authId)
                ->where('receiver_id', $id);
        })->orWhere(function ($query) use ($authId, $id) {
            $query->where('sender_id', $id)
                ->where('receiver_id', $authId);
        })->get();
        return ChatResource::collection($chat);
    }

    public function store(Request $request)
    {
        $authId = auth()->user()->id;
        $chat = Chat::create([
            'sender_id' => $authId,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return response()->json([
            'data' => new ChatResource($chat)
        ], 200);
    }
}
