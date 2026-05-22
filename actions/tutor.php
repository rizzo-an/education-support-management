<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Tutor;
use App\Services\TutorService;
use function App\Utils\json_response;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$tutorService = new TutorService();

$dispatcher->post('create', function ($params) use ($tutorService) {
    require_user_authentication();

    $tutor = new Tutor(null, (int) ($params['cooperative_id'] ?? 0), $params['first_name'] ?? null, $params['last_name'] ?? null, $params['telephone_number'] ?? null);

    try {
        $tutorService->create($tutor);
        json_response(['ok' => true, 'message' => 'Tutor created']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('update', function ($params) use ($tutorService) {
    require_user_authentication();

    $tutor = new Tutor((int) ($params['id'] ?? 0), (int) ($params['cooperative_id'] ?? 0), $params['first_name'] ?? null, $params['last_name'] ?? null, $params['telephone_number'] ?? null);

    try {
        $tutorService->update($tutor);
        json_response(['ok' => true, 'message' => 'Tutor updated']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('delete', function ($params) use ($tutorService) {
    require_user_authentication();

    $tutor = new Tutor((int) ($params['id'] ?? 0), null, null, null, null);

    try {
        $tutorService->delete($tutor);
        json_response(['ok' => true, 'message' => 'Tutor deleted']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->dispatch();