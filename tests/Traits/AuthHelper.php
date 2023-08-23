<?php

namespace Tests\Traits;

trait AuthHelper
{
    private function prepareUserData(): array
    {
        return [
            'name' => 'test',
            'surname' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];
    }
}
