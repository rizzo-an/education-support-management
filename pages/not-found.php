<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

	public function __construct() {
		parent::__construct(new MainLayout('Pagina non trovata'), function () { ?>
			<section class="state-card">
				<h1>404</h1>
				<p>Pagina non trovata.</p>
			</section>
		<?php });
	}
};

echo $page->render();
use function App\Utils\plain_response;

plain_response('Not found.', 404);