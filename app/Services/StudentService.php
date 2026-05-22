<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudyType;
use App\Repositories\StudentRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use DateTime;
use Exception;

class StudentService extends Service {

    public function __construct(private StudentRepository $studentRepository = new StudentRepository()) {
    }

    public function get_all(array $filters = []): array {
        return $this->studentRepository->get_all($filters);
    }

    public function get_by_id(int $id): ?Student {
        return $this->studentRepository->get_by_id($id);
    }

    public function create(Student $student): void {
        $this->validate_student($student);

        try {
            $this->studentRepository->create($student);
        } catch (Exception $e) {
            throw new ServiceErrorException('Unable to create student.');
        }
    }

    public function update(Student $student): void {
        $this->validate_student($student);

        if ($this->studentRepository->get_by_id((int) $student->get_id()) === null) {
            throw new ServiceErrorException('Student not found.');
        }

        $this->studentRepository->update($student);
    }

    public function delete(Student $student): void {
        if ($this->studentRepository->get_by_id((int) $student->get_id()) === null) {
            throw new ServiceErrorException('Student not found.');
        }

        $this->studentRepository->delete_by_id((int) $student->get_id());
    }

    private function validate_student(Student $student): void {
        if (!StringUtils::is_valid_text($student->get_first_name(), 255)) {
            throw new ServiceErrorException('Invalid student first name.');
        }

        if (!StringUtils::is_valid_text($student->get_last_name(), 255)) {
            throw new ServiceErrorException('Invalid student last name.');
        }

        if ($student->get_birth_date() === null || $student->get_birth_date() > new DateTime()) {
            throw new ServiceErrorException('Invalid student birth date.');
        }

        if (!StringUtils::is_valid_text($student->get_class_name(), 255)) {
            throw new ServiceErrorException('Invalid student class.');
        }

        if (!StringUtils::is_valid_text($student->get_city(), 255)) {
            throw new ServiceErrorException('Invalid student city.');
        }

        if ($student->get_study_type() === null || StudyType::tryFrom($student->get_study_type()->value) === null) {
            throw new ServiceErrorException('Invalid study type.');
        }

        if (!is_int($student->get_hours()) || $student->get_hours() <= 0) {
            throw new ServiceErrorException('Invalid hours.');
        }
    }
}