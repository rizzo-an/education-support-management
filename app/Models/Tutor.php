<?php

namespace App\Models;

class Tutor {

    public function __construct(
        private ?int $id,
        private ?int $cooperative_id,
        private ?string $first_name,
        private ?string $last_name,
        private ?string $telephone_number
    ) {
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_cooperative_id(): ?int {
        return $this->cooperative_id;
    }

    public function get_first_name(): ?string {
        return $this->first_name;
    }

    public function get_last_name(): ?string {
        return $this->last_name;
    }

    public function get_telephone_number(): ?string {
        return $this->telephone_number;
    }
}