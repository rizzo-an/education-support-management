<?php

namespace App\Components;

use App\Models\Tutor;
use Camezilla\Components\Component;

class TutorItem extends Component {

    public function __construct(private Tutor $tutor, private string $cooperative_name = '') {
        parent::__construct();
    }

    protected function build(): void { ?>
        <tr>
            <td><?= e($this->tutor->get_last_name() . ' ' . $this->tutor->get_first_name()) ?></td>
            <td><?= e($this->cooperative_name) ?></td>
            <td><?= e((string) $this->tutor->get_telephone_number()) ?></td>
        </tr>
    <?php }
}