<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
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

            if ($validateUser->fails() || !Auth::attempt($validateUser->validated())) {
                return response()->json([
                    'error' => __('auth.failed')
                ], Response::HTTP_UNAUTHORIZED);
            }

            $accessToken = $request->bearerToken();

            $token = PersonalAccessToken::findToken($accessToken);

            if ($token) {
                return response()->json([
                    'message' => __('auth.already_log_in'),
                    'token' => $accessToken
                ], Response::HTTP_OK);
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

    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();

        $token = PersonalAccessToken::findToken($accessToken);

        if ($token) {
            $token->delete();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => __('auth.log_out')
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->create($request->validated());

        if (!$user) {
            return response()->json(__('error.server_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return UserResource::make($user);
    }
}
