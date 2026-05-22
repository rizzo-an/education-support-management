<?php

namespace App\Services;

use App\Models\Cooperative;
use App\Repositories\CooperativeRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class CooperativeService extends Service {

    public function __construct(private CooperativeRepository $cooperativeRepository = new CooperativeRepository()) {
    }

    public function get_all(): array {
        return $this->cooperativeRepository->get_all();
    }

    public function get_by_id(int $id): ?Cooperative {
        return $this->cooperativeRepository->get_by_id($id);
    }

    public function create(Cooperative $cooperative): void {
        $this->validate_cooperative($cooperative);

        try {
            $this->cooperativeRepository->create($cooperative);
        } catch (Exception $e) {
            throw new ServiceErrorException('Unable to create cooperative.');
        }
    }

    public function update(Cooperative $cooperative): void {
        $this->validate_cooperative($cooperative);

        if ($this->cooperativeRepository->get_by_id((int) $cooperative->get_id()) === null) {
            throw new ServiceErrorException('Cooperative not found.');
        }

        $this->cooperativeRepository->update($cooperative);
    }

    public function delete(Cooperative $cooperative): void {
        if ($this->cooperativeRepository->get_by_id((int) $cooperative->get_id()) === null) {
            throw new ServiceErrorException('Cooperative not found.');
        }

        $this->cooperativeRepository->delete_by_id((int) $cooperative->get_id());
    }

    private function validate_cooperative(Cooperative $cooperative): void {
        if (!StringUtils::is_valid_text($cooperative->get_name(), 255)) {
            throw new ServiceErrorException('Invalid cooperative name.');
        }
    }
}