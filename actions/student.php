<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Student;
use App\Models\StudyType;
use App\Services\StudentService;
use function App\Utils\json_response;
use Camezilla\Dispatchers\Dispatcher;
use DateTime;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$studentService = new StudentService();

$parse_ids = static function ($value): array {
    if (!is_array($value)) {
        return [];
    }

    return array_values(array_filter(array_map('intval', $value)));
};

$dispatcher->post('create', function ($params) use ($studentService, $parse_ids) {
    require_user_authentication();

    $student = new Student(
        null,
        $params['first_name'] ?? null,
        $params['last_name'] ?? null,
        new DateTime($params['birth_date'] ?? 'now'),
        $params['class_name'] ?? null,
        $params['city'] ?? null,
        StudyType::from($params['study_type'] ?? StudyType::Differenziata->value),
        (int) ($params['hours'] ?? 0),
        $parse_ids($params['teacher_ids'] ?? []),
        $parse_ids($params['tutor_ids'] ?? [])
    );

    try {
        $studentService->create($student);
        json_response(['ok' => true, 'message' => 'Student created']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('update', function ($params) use ($studentService, $parse_ids) {
    require_user_authentication();

    $student = new Student(
        (int) ($params['id'] ?? 0),
        $params['first_name'] ?? null,
        $params['last_name'] ?? null,
        new DateTime($params['birth_date'] ?? 'now'),
        $params['class_name'] ?? null,
        $params['city'] ?? null,
        StudyType::from($params['study_type'] ?? StudyType::Differenziata->value),
        (int) ($params['hours'] ?? 0),
        $parse_ids($params['teacher_ids'] ?? []),
        $parse_ids($params['tutor_ids'] ?? [])
    );

    try {
        $studentService->update($student);
        json_response(['ok' => true, 'message' => 'Student updated']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('delete', function ($params) use ($studentService) {
    require_user_authentication();

    $student = new Student((int) ($params['id'] ?? 0), null, null, null, null, null, null, null);

    try {
        $studentService->delete($student);
        json_response(['ok' => true, 'message' => 'Student deleted']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->dispatch();