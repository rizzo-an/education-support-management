<?php

namespace App\Services;

use App\Models\Tutor;
use App\Repositories\CooperativeRepository;
use App\Repositories\TutorRepository;
use App\Utils\StringUtils;
use Camezilla\Exceptions\ServiceErrorException;
use Camezilla\Services\Service;
use Exception;

class TutorService extends Service {

    public function __construct(
        private TutorRepository $tutorRepository = new TutorRepository(),
        private CooperativeRepository $cooperativeRepository = new CooperativeRepository()
    ) {
    }

    public function get_all(): array {
        return $this->tutorRepository->get_all();
    }

    public function get_by_id(int $id): ?Tutor {
        return $this->tutorRepository->get_by_id($id);
    }

    public function create(Tutor $tutor): void {
        $this->validate_tutor($tutor);

        try {
            $this->tutorRepository->create($tutor);
        } catch (Exception $e) {
            throw new ServiceErrorException('Unable to create tutor.');
        }
    }

    public function update(Tutor $tutor): void {
        $this->validate_tutor($tutor);

        if ($this->tutorRepository->get_by_id((int) $tutor->get_id()) === null) {
            throw new ServiceErrorException('Tutor not found.');
        }

        $this->tutorRepository->update($tutor);
    }

    public function delete(Tutor $tutor): void {
        if ($this->tutorRepository->get_by_id((int) $tutor->get_id()) === null) {
            throw new ServiceErrorException('Tutor not found.');
        }

        $this->tutorRepository->delete_by_id((int) $tutor->get_id());
    }

    private function validate_tutor(Tutor $tutor): void {
        if ($this->cooperativeRepository->get_by_id((int) $tutor->get_cooperative_id()) === null) {
            throw new ServiceErrorException('Invalid cooperative.');
        }

        if (!StringUtils::is_valid_text($tutor->get_first_name(), 255)) {
            throw new ServiceErrorException('Invalid tutor first name.');
        }

        if (!StringUtils::is_valid_text($tutor->get_last_name(), 255)) {
            throw new ServiceErrorException('Invalid tutor last name.');
        }

        if (!StringUtils::is_valid_phone($tutor->get_telephone_number())) {
            throw new ServiceErrorException('Invalid tutor telephone number.');
        }
    }
}