<?php

namespace App\Models;

class User {

    public function __construct(
        private ?int $id,
        private ?string $email,
        private ?string $password_hash
    ) {
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_email(): ?string {
        return $this->email;
    }

    public function get_password_hash(): ?string {
        return $this->password_hash;
    }

    public function hash_password(): void {
        if ($this->password_hash !== null && $this->password_hash !== '') {
            $this->password_hash = hash_password($this->password_hash);
        }
    }
}