<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create($request->validated());

            return UserResource::make($user);
        } catch (Throwable $e) {
            Log::error($e);

            return response()->json(__('errors.server_error'), 500);
        }
    }
}
