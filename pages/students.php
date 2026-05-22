<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\StudentGroup;
use App\Layouts\MainLayout;
use App\Services\StudentService;
use App\Services\TeacherService;
use App\Services\TutorService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $studentService = new StudentService();
        $teacherService = new TeacherService();
        $tutorService = new TutorService();

        $filters = [
            'first_name' => $_GET['first_name'] ?? '',
            'last_name' => $_GET['last_name'] ?? '',
            'class_name' => $_GET['class_name'] ?? '',
            'city' => $_GET['city'] ?? '',
            'study_type' => $_GET['study_type'] ?? '',
            'hours' => $_GET['hours'] ?? '',
            'teacher_id' => $_GET['teacher_id'] ?? '',
            'tutor_id' => $_GET['tutor_id'] ?? '',
        ];

        $students = $studentService->get_all($filters);

        parent::__construct(new MainLayout('Studenti'), function () use ($students, $filters, $teacherService, $tutorService) {
            echo new StudentGroup($students, $filters, $teacherService->get_all(), $tutorService->get_all());
        });
    }
};

echo $page->render();