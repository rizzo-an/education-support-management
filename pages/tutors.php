<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\TutorGroup;
use App\Layouts\MainLayout;
use App\Repositories\CooperativeRepository;
use App\Services\TutorService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $tutorService = new TutorService();
        $cooperatives = (new CooperativeRepository())->get_all();
        $cooperative_labels = [];

        foreach ($cooperatives as $cooperative) {
            $cooperative_labels[$cooperative->get_id()] = $cooperative->get_name();
        }

        parent::__construct(new MainLayout('Tutor'), function () use ($tutorService, $cooperative_labels) {
            echo new TutorGroup($tutorService->get_all(), $cooperative_labels);
        });
    }
};

echo $page->render();