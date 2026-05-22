<?php

namespace App\Models;

class Cooperative {

    public function __construct(
        private ?int $id,
        private ?string $name
    ) {
    }

    public function get_id(): ?int {
        return $this->id;
    }

    public function get_name(): ?string {
        return $this->name;
    }
}