<?php

namespace Tests\Traits;

use App\Models\User;

trait AuthHelper
{
    protected function prepareUserData(): array
    {
        return [
            'name' => 'test',
            'surname' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];
    }
}
