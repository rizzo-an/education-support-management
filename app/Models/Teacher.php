<?php

namespace App\Models;

class Teacher {

    public function __construct(
        private ?int $id,
        private ?string $first_name,
        private ?string $last_name,
        private ?string $email
    ) {
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_first_name(): ?string {
        return $this->first_name;
    }

    public function get_last_name(): ?string {
        return $this->last_name;
    }

    public function get_email(): ?string {
        return $this->email;
    }
}