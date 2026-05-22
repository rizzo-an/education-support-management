<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\UserGroup;
use App\Layouts\MainLayout;
use App\Services\UserService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $users = (new UserService())->get_all();

        parent::__construct(new MainLayout('Utenti'), function () use ($users) {
            echo new UserGroup($users);
        });
    }
};

echo $page->render();