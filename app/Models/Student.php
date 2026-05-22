<?php

namespace App\Models;

use DateTime;

class Student {

    public function __construct(
        private ?int $id,
        private ?string $first_name,
        private ?string $last_name,
        private ?DateTime $birth_date,
        private ?string $class_name,
        private ?string $city,
        private ?StudyType $study_type,
        private ?int $hours,
        private array $teacher_labels = [],
        private array $tutor_labels = []
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

    public function get_birth_date(): ?DateTime {
        return $this->birth_date;
    }

    public function get_class_name(): ?string {
        return $this->class_name;
    }

    public function get_city(): ?string {
        return $this->city;
    }

    public function get_study_type(): ?StudyType {
        return $this->study_type;
    }

    public function get_hours(): ?int {
        return $this->hours;
    }

    public function get_teacher_labels(): array {
        return $this->teacher_labels;
    }

    public function get_tutor_labels(): array {
        return $this->tutor_labels;
    }
}