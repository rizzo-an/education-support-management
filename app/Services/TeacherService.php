<?php

namespace App\Services;

use App\Models\Teacher;
use App\Repositories\TeacherRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class TeacherService extends Service {

    public function __construct(private TeacherRepository $teacherRepository = new TeacherRepository()) {
    }

    public function get_all(): array {
        return $this->teacherRepository->get_all();
    }

    public function get_by_id(int $id): ?Teacher {
        return $this->teacherRepository->get_by_id($id);
    }

    public function create(Teacher $teacher): void {
        $this->validate_teacher($teacher);

        try {
            $this->teacherRepository->create($teacher);
        } catch (Exception $e) {
            throw new ServiceErrorException('Unable to create teacher.');
        }
    }

    public function update(Teacher $teacher): void {
        $this->validate_teacher($teacher);

        if ($this->teacherRepository->get_by_id((int) $teacher->get_id()) === null) {
            throw new ServiceErrorException('Teacher not found.');
        }

        $this->teacherRepository->update($teacher);
    }

    public function delete(Teacher $teacher): void {
        if ($this->teacherRepository->get_by_id((int) $teacher->get_id()) === null) {
            throw new ServiceErrorException('Teacher not found.');
        }

        $this->teacherRepository->delete_by_id((int) $teacher->get_id());
    }

    private function validate_teacher(Teacher $teacher): void {
        if (!StringUtils::is_valid_text($teacher->get_first_name(), 255)) {
            throw new ServiceErrorException('Invalid teacher first name.');
        }

        if (!StringUtils::is_valid_text($teacher->get_last_name(), 255)) {
            throw new ServiceErrorException('Invalid teacher last name.');
        }

        if (!StringUtils::is_valid_email($teacher->get_email())) {
            throw new ServiceErrorException('Invalid teacher email.');
        }
    }
}