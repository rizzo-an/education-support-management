<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\CooperativeGroup;
use App\Layouts\MainLayout;
use App\Repositories\CooperativeRepository;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $cooperatives = (new CooperativeRepository())->get_all();

        parent::__construct(new MainLayout('Cooperative'), function () use ($cooperatives) {
            echo new CooperativeGroup($cooperatives);
        });
    }
};

echo $page->render();