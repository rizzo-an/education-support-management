<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\User;
use App\Services\UserService;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$userService = new UserService();

$dispatcher->post('create', function ($params) use ($userService) {
    require_user_authentication();

    $user = new User(null, $params['email'] ?? null, $params['password'] ?? null);

    try {
        $userService->create($user);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('update', function ($params) use ($userService) {
    require_user_authentication();

    $user = new User((int) ($params['id'] ?? 0), $params['email'] ?? null, $params['password'] ?? null);

    try {
        $userService->update($user);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('delete', function ($params) use ($userService) {
    require_user_authentication();

    $user = new User((int) ($params['id'] ?? 0), null, null);

    try {
        $userService->delete($user);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->dispatch();