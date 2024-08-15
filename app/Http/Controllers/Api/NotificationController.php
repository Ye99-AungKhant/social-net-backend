<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNoti()
    {
        $authId = auth()->user()->id;
        $noti = Notification::whereIn('post_id', function ($query) use ($authId) {
            $query->select('id')
                ->from('posts')
                ->where('user_id', $authId);
        })->orderBy('id', 'DESC')->get();

        return response()->json([
            'success' => true,
            'notification' => NotificationResource::collection($noti)
        ]);
    }

    public function readNoti(Request $request)
    {
        $noti  = Notification::where('id', $request->notiId)->update(['read' => true]);
        return response()->json([
            'success' => true,
        ]);
    }

    public function markAsReadAll(Request $request)
    {
        $notiData = $request->notiId;

        foreach ($notiData as $data) {
            $noti = Notification::find($data);
            if ($noti) {
                $noti->read = true;
                $noti->save();
            }
        }
        return response()->json([
            'success' => true,
        ]);
    }
}
