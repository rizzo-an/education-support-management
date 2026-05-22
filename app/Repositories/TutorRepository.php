<?php

namespace App\Repositories;

use App\Models\Tutor;
use Camezilla\Exceptions\RepositoryErrorException;
use Camezilla\Repositories\Repository;
use Exception;

class TutorRepository extends Repository {

    public function get_all(): array {
        try {
            $rows = $this->database->query('SELECT * FROM tutors ORDER BY last_name, first_name')->fetchAll();
            $tutors = [];

            foreach ($rows as $row) {
                $tutors[] = new Tutor((int) $row['id'], (int) $row['cooperative_id'], $row['first_name'], $row['last_name'], $row['telephone_number']);
            }

            return $tutors;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting tutors: ' . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?Tutor {
        try {
            $query = $this->database->prepare('SELECT * FROM tutors WHERE id = :id');
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return new Tutor((int) $row['id'], (int) $row['cooperative_id'], $row['first_name'], $row['last_name'], $row['telephone_number']);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting tutor: ' . $e->getMessage());
        }
    }

    public function create(Tutor $tutor): int {
        try {
            $query = $this->database->prepare('INSERT INTO tutors (cooperative_id, first_name, last_name, telephone_number) VALUES (:cooperative_id, :first_name, :last_name, :telephone_number)');
            $query->execute([
                'cooperative_id' => $tutor->get_cooperative_id(),
                'first_name' => $tutor->get_first_name(),
                'last_name' => $tutor->get_last_name(),
                'telephone_number' => $tutor->get_telephone_number(),
            ]);

            return $this->get_last_inserted_id('SELECT id FROM tutors WHERE cooperative_id = :cooperative_id AND first_name = :first_name AND last_name = :last_name AND telephone_number = :telephone_number ORDER BY id DESC LIMIT 1', [
                'cooperative_id' => $tutor->get_cooperative_id(),
                'first_name' => $tutor->get_first_name(),
                'last_name' => $tutor->get_last_name(),
                'telephone_number' => $tutor->get_telephone_number(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error creating tutor: ' . $e->getMessage());
        }
    }

    public function update(Tutor $tutor): void {
        try {
            $query = $this->database->prepare('UPDATE tutors SET cooperative_id = :cooperative_id, first_name = :first_name, last_name = :last_name, telephone_number = :telephone_number WHERE id = :id');
            $query->execute([
                'id' => $tutor->get_id(),
                'cooperative_id' => $tutor->get_cooperative_id(),
                'first_name' => $tutor->get_first_name(),
                'last_name' => $tutor->get_last_name(),
                'telephone_number' => $tutor->get_telephone_number(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error updating tutor: ' . $e->getMessage());
        }
    }

    public function delete_by_id(int $id): void {
        try {
            $query = $this->database->prepare('DELETE FROM tutors WHERE id = :id');
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error deleting tutor: ' . $e->getMessage());
        }
    }

    private function get_last_inserted_id(string $sql, array $params): int {
        $query = $this->database->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();

        return $row === false ? 0 : (int) $row['id'];
    }
}