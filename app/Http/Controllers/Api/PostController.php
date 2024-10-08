<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Media;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'like', 'media')->withCount('like', 'comment')->orderBy('updated_at', 'DESC')->simplePaginate(10);
        return PostResource::collection($posts)->additional(['success' => true]);
    }

    public function friendPost()
    {
        $user = auth()->user();
        $friendPosts = $user->friendsPosts()->with('like', 'media')->withCount('like', 'comment')->orderBy('updated_at', 'DESC')->simplePaginate(10);
        return PostResource::collection($friendPosts)->additional(['success' => true]);
    }

    public function post($id)
    {
        $post = Post::with('user', 'like', 'media')->withCount('like', 'comment')->where('id', $id)->first();
        return response()->json([
            'success' => true,
            'data' => new PostResource($post)
        ]);
    }

    public function create(Request $request)
    {
        if ($request->image !== null) {
            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->content = $request->content;
            $post->status = $request->status;
            $post->save();

            foreach ($request->image as $value) {
                $media = new Media();
                $media->post_id = $post->id;
                $media->url = $value;
                $media->type = 'Post';
                $media->save();
            }

            return response()->json([
                'success' => true,
                'data' => [new PostResource($post)]
            ], 200);
        } else {
            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->content = $request->content;
            $post->status = $request->status;
            $post->save();

            return response()->json([
                'success' => true,
                'data' => [new PostResource($post)]
            ], 200);
        }
    }

    public function updatePost(Request $request)
    {
        if ($request->image !== null) {
            $post = Post::find($request->id);
            $post->content = $request->content;
            $post->save();

            foreach ($request->image as $value) {
                $media = new Media();
                $media->post_id = $post->id;
                $media->url = $value;
                $media->type = 'Post';
                $media->save();
            }

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ], 200);
        } else {
            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->content = $request->content;
            $post->status = $request->status;
            $post->save();

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ], 200);
        }
    }

    public function deleteMedia(Request $request)
    {
        Media::where('url', $request->media)->delete();
    }

    public function postDelete($id)
    {
        Post::where('id', $id)->delete();
        return response()->json(['success' => true], 200);
    }

    public function like(Request $request)
    {
        $authUserId = auth()->user()->id;
        $postId = $request->postId;
        $like = new Like();
        $liked = $like->where('post_id', $postId)->where('user_id', $authUserId)->first();
        if (!$liked) {
            $like->post_id = $postId;
            $like->user_id = $authUserId;
            $like->save();

            $noti = Notification::create([
                'type' => 'Like',
                'content' => 'reacted to your post',
                'post_id' => $postId,
                'user_id' => $authUserId,
            ]);

            return response()->json([
                'liked' => true,
                'data' => $like
            ], 200);
        }
        $liked->delete();
        Notification::where('post_id', $postId)->delete();
        return response()->json([
            'unliked' => true,
            'data' => ['auth_id' => $authUserId, 'post_id' => $postId]
        ], 200);
    }
}
