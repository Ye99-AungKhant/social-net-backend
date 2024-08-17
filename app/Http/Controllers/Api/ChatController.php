<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\ChatMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index($id)
    {
        $authId = auth()->user()->id;
        $chat = Chat::with('media')->where(function ($query) use ($authId, $id) {
            $query->where('sender_id', $authId)
                ->where('receiver_id', $id);
        })->orWhere(function ($query) use ($authId, $id) {
            $query->where('sender_id', $id)
                ->where('receiver_id', $authId);
        })->get();

        return ChatResource::collection($chat);
    }

    public function lastmessage()
    {
        $authId = auth()->user()->id;
        // $lastMessages = DB::table('chats as c1')
        //     ->join(DB::raw('(SELECT MAX(id) as last_id FROM chats GROUP BY sender_id, receiver_id) as c2'), 'c1.id', '=', 'c2.last_id')
        //     ->select('c1.*')
        //     ->get();
        $lastMessages = DB::table('chats as c1')
            ->join(
                DB::raw('(SELECT MAX(id) as last_id FROM chats WHERE sender_id = ? OR receiver_id = ? GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)) as c2'),
                'c1.id',
                '=',
                'c2.last_id'
            )
            ->select('c1.*')
            ->setBindings([$authId, $authId])
            ->get();


        return response()->json([
            'data' => $lastMessages
        ]);
    }

    public function store(Request $request)
    {
        $authId = auth()->user()->id;
        if ($request->media != null) {
            $chat = Chat::create([
                'sender_id' => $authId,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            foreach ($request->media as $value) {
                $chatMedia = new ChatMedia();
                $chatMedia->chat_id = $chat->id;
                $chatMedia->url = $value['url'];
                $chatMedia->save();
            }

            return response()->json([
                'data' => $chat
            ], 200);
        } else {
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

    public function markAsRead(Request $request)
    {
        $authId = auth()->user()->id;
        $chat = Chat::where('sender_id', $request->senderId)->where('receiver_id', $authId)->update(['read' => true]);
        return response()->json(['status' => 'success', 'data' => $chat]);
    }
}
