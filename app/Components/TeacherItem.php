<?php

namespace App\Components;

use App\Models\Teacher;
use Camezilla\Components\Component;

class TeacherItem extends Component {

    public function __construct(private Teacher $teacher) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <tr>
            <td><?= e($this->teacher->get_last_name() . ' ' . $this->teacher->get_first_name()) ?></td>
            <td><?= e((string) $this->teacher->get_email()) ?></td>
        </tr>
    <?php }
}