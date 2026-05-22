<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\User;
use App\Services\AuthenticationService;
use function App\Utils\json_response;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$authenticationService = new AuthenticationService();

$dispatcher->post('login', function ($params) use ($authenticationService) {
    $user = new User(null, $params['email'] ?? null, $params['password'] ?? null);

    try {
        $authenticationService->login($user);
        json_response([
            'ok' => true,
            'message' => 'Authenticated',
        ]);
    } catch (Exception $e) {
        json_response([
            'ok' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
});

$dispatcher->get('logout', function () use ($authenticationService) {
    try {
        $authenticationService->logout();
        json_response([
            'ok' => true,
            'message' => 'Logged out',
        ]);
    } catch (Exception $e) {
        json_response([
            'ok' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
});

$dispatcher->dispatch();