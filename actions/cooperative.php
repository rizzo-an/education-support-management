<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Cooperative;
use App\Services\CooperativeService;
use function App\Utils\json_response;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$cooperativeService = new CooperativeService();

$dispatcher->post('create', function ($params) use ($cooperativeService) {
    require_user_authentication();

    $cooperative = new Cooperative(null, $params['name'] ?? null);

    try {
        $cooperativeService->create($cooperative);
        json_response(['ok' => true, 'message' => 'Cooperative created']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('update', function ($params) use ($cooperativeService) {
    require_user_authentication();

    $cooperative = new Cooperative((int) ($params['id'] ?? 0), $params['name'] ?? null);

    try {
        $cooperativeService->update($cooperative);
        json_response(['ok' => true, 'message' => 'Cooperative updated']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->post('delete', function ($params) use ($cooperativeService) {
    require_user_authentication();

    $cooperative = new Cooperative((int) ($params['id'] ?? 0), null);

    try {
        $cooperativeService->delete($cooperative);
        json_response(['ok' => true, 'message' => 'Cooperative deleted']);
    } catch (Exception $e) {
        json_response(['ok' => false, 'message' => $e->getMessage()], 400);
    }
});

$dispatcher->dispatch();