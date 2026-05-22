<?php

namespace App\Repositories;

use App\Models\User;
use Camezilla\Exceptions\RepositoryErrorException;
use Camezilla\Repositories\Repository;
use Exception;

class UserRepository extends Repository {

    public function get_all(): array {
        try {
            $rows = $this->database->query('SELECT * FROM users ORDER BY email')->fetchAll();
            $users = [];

            foreach ($rows as $row) {
                $users[] = new User((int) $row['id'], $row['email'], $row['password_hash']);
            }

            return $users;
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting users: ' . $e->getMessage());
        }
    }

    public function get_by_id(int $id): ?User {
        try {
            $query = $this->database->prepare('SELECT * FROM users WHERE id = :id');
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return new User((int) $row['id'], $row['email'], $row['password_hash']);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting user: ' . $e->getMessage());
        }
    }

    public function get_by_email(string $email): ?User {
        try {
            $query = $this->database->prepare('SELECT * FROM users WHERE email = :email');
            $query->execute(['email' => $email]);
            $row = $query->fetch();

            if ($row === false) {
                return null;
            }

            return new User((int) $row['id'], $row['email'], $row['password_hash']);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error getting user by email: ' . $e->getMessage());
        }
    }

    public function create(User $user): int {
        try {
            $query = $this->database->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)');
            $query->execute([
                'email' => $user->get_email(),
                'password_hash' => $user->get_password_hash(),
            ]);

            return $this->get_last_inserted_id('SELECT id FROM users WHERE email = :email ORDER BY id DESC LIMIT 1', [
                'email' => $user->get_email(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error creating user: ' . $e->getMessage());
        }
    }

    public function update(User $user): void {
        try {
            $query = $this->database->prepare('UPDATE users SET email = :email, password_hash = :password_hash WHERE id = :id');
            $query->execute([
                'id' => $user->get_id(),
                'email' => $user->get_email(),
                'password_hash' => $user->get_password_hash(),
            ]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error updating user: ' . $e->getMessage());
        }
    }

    public function delete_by_id(int $id): void {
        try {
            $query = $this->database->prepare('DELETE FROM users WHERE id = :id');
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            throw new RepositoryErrorException('Error deleting user: ' . $e->getMessage());
        }
    }

    private function get_last_inserted_id(string $sql, array $params): int {
        $query = $this->database->prepare($sql);
        $query->execute($params);
        $row = $query->fetch();

        return $row === false ? 0 : (int) $row['id'];
    }
}