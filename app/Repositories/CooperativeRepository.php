<?php

namespace App\Repositories;

use App\Models\Cooperative;
use Camezilla\Exceptions\RepositoryErrorException;
use Camezilla\Repositories\Repository;
use Exception;

class CooperativeRepository extends Repository {

    public function get_all(): array {
        try {
            $rows = $this->database->query('SELECT * FROM cooperatives ORDER BY name')->fetchAll();
            $cooperatives = [];

            foreach ($rows as $row) {
                $cooperatives[] = new Cooperative((int) $row['id'], $row['name']);
            }

            return $cooperatives;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting cooperatives: ' . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?Cooperative {
        try {
            $query = $this->database->prepare('SELECT * FROM cooperatives WHERE id = :id');
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return new Cooperative((int) $row['id'], $row['name']);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting cooperative: ' . $e->getMessage());
        }
    }

    public function create(Cooperative $cooperative): int {
        try {
            $query = $this->database->prepare('INSERT INTO cooperatives (name) VALUES (:name)');
            $query->execute(['name' => $cooperative->get_name()]);

            return $this->get_last_inserted_id('SELECT id FROM cooperatives WHERE name = :name ORDER BY id DESC LIMIT 1', [
                'name' => $cooperative->get_name(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error creating cooperative: ' . $e->getMessage());
        }
    }

    public function update(Cooperative $cooperative): void {
        try {
            $query = $this->database->prepare('UPDATE cooperatives SET name = :name WHERE id = :id');
            $query->execute([
                'id' => $cooperative->get_id(),
                'name' => $cooperative->get_name(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error updating cooperative: ' . $e->getMessage());
        }
    }

    public function delete_by_id(int $id): void {
        try {
            $query = $this->database->prepare('DELETE FROM cooperatives WHERE id = :id');
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error deleting cooperative: ' . $e->getMessage());
        }
    }

    private function get_last_inserted_id(string $sql, array $params): int {
        $query = $this->database->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();

        return $row === false ? 0 : (int) $row['id'];
    }
}