<?php

namespace App\Services;

use App\Models\User;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class AuthenticationService extends Service {

    public function __construct(private UserService $userService = new UserService()) {
    }

    public function login(User $user): void {
        if ($user->get_email() === null || $user->get_password_hash() === null) {
            throw new ServiceErrorException('Missing credentials.');
        }

        try {
            $existing_user = $this->userService->get_by_email($user->get_email());

            if ($existing_user === null) {
                throw new ServiceErrorException('Invalid credentials.');
            }

            if (!password_verify($user->get_password_hash(), (string) $existing_user->get_password_hash())) {
                throw new ServiceErrorException('Invalid credentials.');
            }

            authenticate_user((int) $existing_user->get_id(), (string) $existing_user->get_email());
        } catch (Exception $e) {
            if ($e instanceof ServiceErrorException) {
                throw $e;
            }

            throw new ServiceErrorException('Unable to log in.');
        }
    }

    public function logout(): void {
        remove_user_authentication();
    }
}