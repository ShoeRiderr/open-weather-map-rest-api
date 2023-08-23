<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => ['required', 'email'],
                    'password' => ['required'],
                ]
            );

            if ($validateUser->fails() || !Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'error' => __('auth.failed')
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'message' => __('auth.log_in'),
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error($e);

            return response()->json([
                'error' => __('error.server_error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function register(RegisterRequest $request)
    {
        $response = $this->userService->create($request->validated());

        if (!$response) {
            return response()->json(__('error.server_error'), 500);
        }

        return $response;
    }
}
