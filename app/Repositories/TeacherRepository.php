<?php

namespace App\Repositories;

use App\Models\Teacher;
use Camezilla\Exceptions\RepositoryErrorException;
use Camezilla\Repositories\Repository;
use Exception;

class TeacherRepository extends Repository {

    public function get_all(): array {
        try {
            $rows = $this->database->query('SELECT * FROM teachers ORDER BY last_name, first_name')->fetchAll();
            $teachers = [];

            foreach ($rows as $row) {
                $teachers[] = new Teacher((int) $row['id'], $row['first_name'], $row['last_name'], $row['email']);
            }

            return $teachers;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting teachers: ' . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?Teacher {
        try {
            $query = $this->database->prepare('SELECT * FROM teachers WHERE id = :id');
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return new Teacher((int) $row['id'], $row['first_name'], $row['last_name'], $row['email']);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting teacher: ' . $e->getMessage());
        }
    }

    public function create(Teacher $teacher): int {
        try {
            $query = $this->database->prepare('INSERT INTO teachers (first_name, last_name, email) VALUES (:first_name, :last_name, :email)');
            $query->execute([
                'first_name' => $teacher->get_first_name(),
                'last_name' => $teacher->get_last_name(),
                'email' => $teacher->get_email(),
            ]);

            return $this->get_last_inserted_id('SELECT id FROM teachers WHERE email = :email ORDER BY id DESC LIMIT 1', [
                'email' => $teacher->get_email(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error creating teacher: ' . $e->getMessage());
        }
    }

    public function update(Teacher $teacher): void {
        try {
            $query = $this->database->prepare('UPDATE teachers SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id');
            $query->execute([
                'id' => $teacher->get_id(),
                'first_name' => $teacher->get_first_name(),
                'last_name' => $teacher->get_last_name(),
                'email' => $teacher->get_email(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error updating teacher: ' . $e->getMessage());
        }
    }

    public function delete_by_id(int $id): void {
        try {
            $query = $this->database->prepare('DELETE FROM teachers WHERE id = :id');
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error deleting teacher: ' . $e->getMessage());
        }
    }

    private function get_last_inserted_id(string $sql, array $params): int {
        $query = $this->database->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();

        return $row === false ? 0 : (int) $row['id'];
    }
}