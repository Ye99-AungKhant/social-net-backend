<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function signup(SignUpRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('socialnet88');
        $response_token = $token->plainTextToken;
        return response()->json([
            'success' => true,
            'data' => ['access_token' => $response_token, 'user_data' => new UserResource($user)]
        ], 200);
    }

    public function signin(SignInRequest $request)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = auth()->user();
                if ($user instanceof \App\Models\User) {
                    $token = $user->createToken('socialnet88');
                    $response_token = $token->plainTextToken;

                    return response()->json([
                        'success' => true,
                        'data' => ['access_token' => $response_token, 'user_data' => new UserResource($user)]
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your email or password do not match. Please try again.'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        $userCheck = User::where('email', $request->email)->first();

        try {
            if ($userCheck) {
                $user = $userCheck;
                if ($user instanceof \App\Models\User) {
                    $token = $user->createToken('socialnet88');
                    $response_token = $token->plainTextToken;

                    return response()->json([
                        'success' => true,
                        'data' => ['access_token' => $response_token, 'user_data' => new UserResource($user)]
                    ], 200);
                }
            } else {

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(str()->random()),
                ]);
                $token = $user->createToken('socialnet88');
                $response_token = $token->plainTextToken;
                return response()->json([
                    'success' => true,
                    'data' => ['access_token' => $response_token, 'user_data' => new UserResource($user)]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true
        ], 200);
    }
}
