<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\Login;
use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

if (is_user_authenticated()) {
	header('Location: ' . page('index.php'));
	exit();
}

$page = new class extends Page {

	public function __construct() {
		parent::__construct(new MainLayout('Login'), function () { ?>
			<?= new Login() ?>
		<?php });
	}
};

echo $page->render();