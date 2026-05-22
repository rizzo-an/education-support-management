<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class UserService extends Service {

    public function __construct(private UserRepository $userRepository = new UserRepository()) {
    }

    public function get_all(): array {
        return $this->userRepository->get_all();
    }

    public function get_by_id(int $id): ?User {
        return $this->userRepository->get_by_id($id);
    }

    public function get_by_email(string $email): ?User {
        return $this->userRepository->get_by_email($email);
    }

    public function create(User $user): void {
        $this->validate_user($user);

        if ($this->userRepository->get_by_email((string) $user->get_email()) !== null) {
            throw new ServiceErrorException('Email already exists.');
        }

        $password_hash = $user->get_password_hash() ?? '';
        if (password_get_info($password_hash)['algo'] === 0) {
            $password_hash = hash_password($password_hash);
        }

        $this->userRepository->create(new User(null, $user->get_email(), $password_hash));
    }

    public function update(User $user): void {
        $this->validate_user($user);

        if ($this->userRepository->get_by_id((int) $user->get_id()) === null) {
            throw new ServiceErrorException('User not found.');
        }

        $this->userRepository->update($user);
    }

    public function delete(User $user): void {
        if ($this->userRepository->get_by_id((int) $user->get_id()) === null) {
            throw new ServiceErrorException('User not found.');
        }

        $this->userRepository->delete_by_id((int) $user->get_id());
    }

    private function validate_user(User $user): void {
        if (!StringUtils::is_valid_email($user->get_email())) {
            throw new ServiceErrorException('Invalid email address.');
        }

        if (!StringUtils::is_valid_text($user->get_password_hash(), 255)) {
            throw new ServiceErrorException('Invalid password.');
        }
    }
}