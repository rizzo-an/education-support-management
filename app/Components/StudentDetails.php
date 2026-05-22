<?php

namespace App\Components;

use App\Models\Student;
use Camezilla\Components\Component;

class StudentDetails extends Component {

    public function __construct(private Student $student) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="detail-card">
            <div class="detail-card__header">
                <h1><?= e($this->student->get_last_name() . ' ' . $this->student->get_first_name()) ?></h1>
                <a class="button-ghost" href="<?= page('index.php') ?>">Torna all'elenco</a>
            </div>

            <div class="detail-grid">
                <div><strong>Classe</strong><span><?= e((string) $this->student->get_class_name()) ?></span></div>
                <div><strong>Comune</strong><span><?= e((string) $this->student->get_city()) ?></span></div>
                <div><strong>Programmazione</strong><span><?= e((string) $this->student->get_study_type()?->value) ?></span></div>
                <div><strong>Ore</strong><span><?= e((string) $this->student->get_hours()) ?></span></div>
                <div><strong>Data di nascita</strong><span><?= e($this->student->get_birth_date()?->format('d/m/Y') ?? '') ?></span></div>
            </div>

            <div class="detail-lists">
                <div>
                    <strong>Docenti di sostegno</strong>
                    <p><?= e(implode(' | ', $this->student->get_teacher_labels())) ?></p>
                </div>
                <div>
                    <strong>Tutor</strong>
                    <p><?= e(implode(' | ', $this->student->get_tutor_labels())) ?></p>
                </div>
            </div>
        </section>
    <?php }
}