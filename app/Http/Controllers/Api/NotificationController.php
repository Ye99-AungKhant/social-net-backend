<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
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
}
