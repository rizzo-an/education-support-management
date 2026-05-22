<?php

namespace App\Components;

use App\Models\Student;
use Camezilla\Components\Component;

class StudentItem extends Component {

    public function __construct(private Student $student) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <tr>
            <td>
                <strong><?= e($this->student->get_last_name() . ' ' . $this->student->get_first_name()) ?></strong><br>
                <small><?= e($this->student->get_birth_date()?->format('d/m/Y') ?? '') ?></small>
            </td>
            <td><?= e((string) $this->student->get_class_name()) ?></td>
            <td><?= e((string) $this->student->get_city()) ?></td>
            <td><?= e((string) $this->student->get_study_type()?->value) ?></td>
            <td><?= e((string) $this->student->get_hours()) ?></td>
            <td><?= e(implode(' | ', $this->student->get_teacher_labels())) ?></td>
            <td><?= e(implode(' | ', $this->student->get_tutor_labels())) ?></td>
            <td>
                <a class="table-link" href="<?= page('student.php', ['id' => $this->student->get_id()]) ?>">Apri</a>
            </td>
        </tr>
    <?php }
}