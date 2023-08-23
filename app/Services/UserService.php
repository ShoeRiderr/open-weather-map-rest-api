<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    public function delete(User $user): array
    {
        /**
         * @var array
         */
        $responseMessage = [
            'error' => __('error.server_error')
        ];

        /**
         * @var int
         */
        $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        try {
            do {
                if ($user->id !== auth()->user()->id) {
                    $responseMessage = [
                        'error' => __('user.delete.remove_other_user')
                    ];

                    $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                    break;
                }

                if (!$user->delete()) {
                    $responseMessage = [
                        'error' => __('user.delete.fail')
                    ];

                    $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                    break;
                }


                $responseMessage = [
                    'message' => __('user.delete.success')
                ];

                $responseCode = Response::HTTP_OK;
            } while (false);
        } catch (Throwable $e) {
            Log::error($e);

            $responseMessage = [
                'error' => __('error.server_error')
            ];
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return [
            'response_message' => $responseMessage,
            'response_code' => $responseCode,
        ];
    }

    /**
     * @return JsonResource|bool
     */
    public function create(array $data)
    {
        try {
            $user = User::create($data);;

            return UserResource::make($user);
        } catch (Throwable $e) {
            Log::error($e);

            return false;
        }
    }
}
