<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

	public function __construct() {
		parent::__construct(new MainLayout('Errore'), function () { ?>
			<section class="state-card">
				<h1>Errore</h1>
				<p>Si è verificato un problema durante la richiesta.</p>
			</section>
		<?php });
	}
};

echo $page->render();
use function App\Utils\plain_response;

plain_response('Internal server error.', 500);