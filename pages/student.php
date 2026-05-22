<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\StudentDetails;
use App\Components\StudentForm;
use App\Layouts\MainLayout;
use App\Services\StudentService;
use App\Services\TeacherService;
use App\Services\TutorService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $student_id = (int) ($_GET['id'] ?? 0);
        $studentService = new StudentService();
        $teacherService = new TeacherService();
        $tutorService = new TutorService();
        $student = $studentService->get_by_id($student_id);

        parent::__construct(new MainLayout('Studente'), function () use ($student, $teacherService, $tutorService) {
            if (!$student) {
                ?>
                <section class="state-card">
                    <h1>Studente non trovato</h1>
                    <p>Il record richiesto non esiste.</p>
                </section>
                <?php
                return;
            }

            echo new StudentDetails($student);
            echo new StudentForm($student, $teacherService->get_all(), $tutorService->get_all());
        });
    }
};

echo $page->render();