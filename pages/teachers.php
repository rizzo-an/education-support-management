<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\TeacherGroup;
use App\Layouts\MainLayout;
use App\Services\TeacherService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $teachers = (new TeacherService())->get_all();

        parent::__construct(new MainLayout('Docenti'), function () use ($teachers) {
            echo new TeacherGroup($teachers);
        });
    }
};

echo $page->render();