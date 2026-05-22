<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\StudyType;
use Camezilla\Exceptions\RepositoryErrorException;
use Camezilla\Repositories\Repository;
use DateTime;
use Exception;

class StudentRepository extends Repository {

    public function get_all(array $filters = []): array {
        try {
            [$sql, $params] = $this->build_filter_query($filters);
            $query = $this->database->prepare($sql);
            $query->execute($params);

            $students = [];
            while (($row = $query->fetch()) !== false) {
                $students[] = $this->map_row($row);
            }

            return $students;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting students: ' . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?Student {
        try {
            $query = $this->database->prepare('SELECT * FROM students WHERE id = :id');
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return $this->map_row($row);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting student: ' . $e->getMessage());
        }
    }

    public function create(Student $student): int {
        try {
            $query = $this->database->prepare('INSERT INTO students (first_name, last_name, birth_date, `class`, city, study_type, hours) VALUES (:first_name, :last_name, :birth_date, :class, :city, :study_type, :hours)');
            $query->execute([
                'first_name' => $student->get_first_name(),
                'last_name' => $student->get_last_name(),
                'birth_date' => $student->get_birth_date()?->format('Y-m-d'),
                'class' => $student->get_class_name(),
                'city' => $student->get_city(),
                'study_type' => $student->get_study_type()?->value,
                'hours' => $student->get_hours(),
            ]);

            $student_id = $this->get_last_inserted_id('SELECT id FROM students WHERE first_name = :first_name AND last_name = :last_name AND birth_date = :birth_date AND `class` = :class AND city = :city AND study_type = :study_type AND hours = :hours ORDER BY id DESC LIMIT 1', [
                'first_name' => $student->get_first_name(),
                'last_name' => $student->get_last_name(),
                'birth_date' => $student->get_birth_date()?->format('Y-m-d'),
                'class' => $student->get_class_name(),
                'city' => $student->get_city(),
                'study_type' => $student->get_study_type()?->value,
                'hours' => $student->get_hours(),
            ]);
            $this->sync_relations($student_id, $student->get_teacher_labels(), $student->get_tutor_labels());

            return $student_id;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error creating student: ' . $e->getMessage());
        }
    }

    public function update(Student $student): void {
        try {
            $query = $this->database->prepare('UPDATE students SET first_name = :first_name, last_name = :last_name, birth_date = :birth_date, `class` = :class, city = :city, study_type = :study_type, hours = :hours WHERE id = :id');
            $query->execute([
                'id' => $student->get_id(),
                'first_name' => $student->get_first_name(),
                'last_name' => $student->get_last_name(),
                'birth_date' => $student->get_birth_date()?->format('Y-m-d'),
                'class' => $student->get_class_name(),
                'city' => $student->get_city(),
                'study_type' => $student->get_study_type()?->value,
                'hours' => $student->get_hours(),
            ]);

            $this->sync_relations((int) $student->get_id(), $student->get_teacher_labels(), $student->get_tutor_labels());
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error updating student: ' . $e->getMessage());
        }
    }

    public function delete_by_id(int $id): void {
        try {
            $query = $this->database->prepare('DELETE FROM students WHERE id = :id');
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error deleting student: ' . $e->getMessage());
        }
    }

    private function build_filter_query(array $filters): array {
        $where = [];
        $params = [];

        if (!empty($filters['first_name'])) {
            $where[] = 's.first_name LIKE :first_name';
            $params['first_name'] = '%' . trim($filters['first_name']) . '%';
        }

        if (!empty($filters['last_name'])) {
            $where[] = 's.last_name LIKE :last_name';
            $params['last_name'] = '%' . trim($filters['last_name']) . '%';
        }

        if (!empty($filters['class_name'])) {
            $where[] = 's.`class` LIKE :class_name';
            $params['class_name'] = '%' . trim($filters['class_name']) . '%';
        }

        if (!empty($filters['city'])) {
            $where[] = 's.city LIKE :city';
            $params['city'] = '%' . trim($filters['city']) . '%';
        }

        if (!empty($filters['study_type'])) {
            $where[] = 's.study_type = :study_type';
            $params['study_type'] = $filters['study_type'];
        }

        if (!empty($filters['hours'])) {
            $where[] = 's.hours = :hours';
            $params['hours'] = (int) $filters['hours'];
        }

        if (!empty($filters['teacher_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM teachers_students ts WHERE ts.student_id = s.id AND ts.teacher_id = :teacher_id)';
            $params['teacher_id'] = (int) $filters['teacher_id'];
        }

        if (!empty($filters['tutor_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM students_tutors st WHERE st.student_id = s.id AND st.tutor_id = :tutor_id)';
            $params['tutor_id'] = (int) $filters['tutor_id'];
        }

        $sql = 'SELECT s.* FROM students s';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY s.last_name, s.first_name';

        return [$sql, $params];
    }

    private function map_row(array $row): Student {
        return new Student(
            (int) $row['id'],
            $row['first_name'],
            $row['last_name'],
            new DateTime($row['birth_date']),
            $row['class'],
            $row['city'],
            StudyType::from($row['study_type']),
            (int) $row['hours'],
            $this->get_teacher_labels_by_student_id((int) $row['id']),
            $this->get_tutor_labels_by_student_id((int) $row['id'])
        );
    }

    private function sync_relations(int $student_id, array $teacher_labels, array $tutor_labels): void {
        $this->database->prepare('DELETE FROM teachers_students WHERE student_id = :student_id')->execute(['student_id' => $student_id]);
        $this->database->prepare('DELETE FROM students_tutors WHERE student_id = :student_id')->execute(['student_id' => $student_id]);

        foreach (array_unique(array_map('intval', $teacher_labels)) as $teacher_id) {
            if ($teacher_id > 0) {
                $this->database->prepare('INSERT INTO teachers_students (student_id, teacher_id) VALUES (:student_id, :teacher_id)')->execute([
                    'student_id' => $student_id,
                    'teacher_id' => $teacher_id,
                ]);
            }
        }

        foreach (array_unique(array_map('intval', $tutor_labels)) as $tutor_id) {
            if ($tutor_id > 0) {
                $this->database->prepare('INSERT INTO students_tutors (student_id, tutor_id) VALUES (:student_id, :tutor_id)')->execute([
                    'student_id' => $student_id,
                    'tutor_id' => $tutor_id,
                ]);
            }
        }
    }

    private function get_teacher_labels_by_student_id(int $student_id): array {
        $query = $this->database->prepare('SELECT CONCAT(t.first_name, " ", t.last_name, " <", t.email, ">") AS label FROM teachers_students ts INNER JOIN teachers t ON t.id = ts.teacher_id WHERE ts.student_id = :student_id ORDER BY t.last_name, t.first_name');
        $query->execute(['student_id' => $student_id]);

        return array_map(static fn(array $row): string => $row['label'], $query->fetchAll());
    }

    private function get_tutor_labels_by_student_id(int $student_id): array {
        $query = $this->database->prepare('SELECT CONCAT(tu.first_name, " ", tu.last_name, " - ", c.name, " (", tu.telephone_number, ")") AS label FROM students_tutors st INNER JOIN tutors tu ON tu.id = st.tutor_id INNER JOIN cooperatives c ON c.id = tu.cooperative_id WHERE st.student_id = :student_id ORDER BY tu.last_name, tu.first_name');
        $query->execute(['student_id' => $student_id]);

        return array_map(static fn(array $row): string => $row['label'], $query->fetchAll());
    }

    private function get_last_inserted_id(string $sql, array $params): int {
        $query = $this->database->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();

        return $row === false ? 0 : (int) $row['id'];
    }
}