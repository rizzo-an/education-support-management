<?php

namespace App\Components;

use App\Models\Cooperative;
use Camezilla\Components\Component;

class CooperativeItem extends Component {

    public function __construct(private Cooperative $cooperative) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <tr>
            <td><?= e((string) $this->cooperative->get_name()) ?></td>
        </tr>
    <?php }
}