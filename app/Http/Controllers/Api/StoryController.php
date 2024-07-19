<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $story = $user->friendsStory()->orderBy('updated_at', 'DESC')->simplePaginate(8);
        return StoryResource::collection($story)->additional(['success' => true], 200);
    }

    public function create(Request $request)
    {
        if ($request->image !== null) {
            $story = new Story();
            $story->user_id = Auth::user()->id;
            $story->content = $request->content ? $request->content : null;
            $story->url = $request->image ? $request->image[0] : null;
            $story->status = $request->status;
            $story->save();

            return response()->json([
                'success' => true,
                'data' => [new StoryResource($story)]
            ], 200);
        } else {
            $story = new Story();
            $story->user_id = Auth::user()->id;
            $story->content = $request->content;
            $story->status = $request->status;
            $story->save();
            return response()->json([
                'success' => true,
                'data' => [new StoryResource($story)]
            ], 200);
        }
    }
}
