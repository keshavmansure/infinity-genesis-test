<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserWithTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function __invoke(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            if (Auth::attempt($credentials)) {
                $user = User::where('email', $request->email)->first();
                $user->access_token = $user->createToken('access')->accessToken;
                return response()->json(new UserWithTokenResource($user), 200);
            }
            return response()->json("Invalid Credentials", 401);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 401);
        }
    }
}
