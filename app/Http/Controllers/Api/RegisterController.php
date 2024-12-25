<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserWithTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        try {
            $data = $request->only(['name', 'email']);
            $data['password'] = Hash::make($request->password);
            $user = User::create($data);
            $user->access_token = $user->createToken('access')->accessToken;
            return response()->json(new UserWithTokenResource($user), 201);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 401);
        }
    }
}
