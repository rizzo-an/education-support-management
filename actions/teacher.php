<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Teacher;
use App\Services\TeacherService;
use function App\Utils\json_response;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$teacherService = new TeacherService();

$dispatcher->post('create', function ($params) use ($teacherService) {
    require_user_authentication();

    $teacher = new Teacher(null, $params['first_name'] ?? null, $params['last_name'] ?? null, $params['email'] ?? null);

    try {
        $teacherService->create($teacher);
        json_response(['ok' => true, 'message' => 'Teacher created']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('update', function ($params) use ($teacherService) {
    require_user_authentication();

    $teacher = new Teacher((int) ($params['id'] ?? 0), $params['first_name'] ?? null, $params['last_name'] ?? null, $params['email'] ?? null);

    try {
        $teacherService->update($teacher);
        json_response(['ok' => true, 'message' => 'Teacher updated']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('delete', function ($params) use ($teacherService) {
    require_user_authentication();

    $teacher = new Teacher((int) ($params['id'] ?? 0), null, null, null);

    try {
        $teacherService->delete($teacher);
        json_response(['ok' => true, 'message' => 'Teacher deleted']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->dispatch();