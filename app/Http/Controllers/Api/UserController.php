<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function destroy(User $user)
    {
        $response = $this->userService->delete($user);

        return response()->json(
            Arr::get($response, 'response_message', []),
            Arr::get($response, 'response_code', Response::HTTP_OK)
        );
    }
}
