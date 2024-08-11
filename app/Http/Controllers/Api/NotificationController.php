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
        $auth = auth()->user();
        $noti = $auth->notificationsFromPosts();

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
}
