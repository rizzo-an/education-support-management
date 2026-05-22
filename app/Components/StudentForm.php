<?php

namespace App\Components;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tutor;
use Camezilla\Components\Component;

class StudentForm extends Component {

    public function __construct(
        private ?Student $student = null,
        private array $teachers = [],
        private array $tutors = []
    ) {
        parent::__construct();
    }

    protected function build(): void {
        $is_update = $this->student !== null;
        $action = $is_update ? 'update' : 'create';
        $title = $is_update ? 'Modifica studente' : 'Nuovo studente';
        $student_id = $this->student?->get_id();
        $teacher_labels = $this->student?->get_teacher_labels() ?? [];
        $tutor_labels = $this->student?->get_tutor_labels() ?? [];
        ?>
        <section class="form-card">
            <div class="form-card__header">
                <h2><?= e($title) ?></h2>
            </div>

            <form class="entity-form" action="<?= get_absolute_url('actions/student.php?path=' . $action . '&redirect=index.php') ?>" method="post">
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?= e((string) $student_id) ?>">
                <?php endif; ?>

                <input type="text" name="first_name" placeholder="Nome" value="<?= e($this->student?->get_first_name() ?? '') ?>" required>
                <input type="text" name="last_name" placeholder="Cognome" value="<?= e($this->student?->get_last_name() ?? '') ?>" required>
                <input type="date" name="birth_date" value="<?= e($this->student?->get_birth_date()?->format('Y-m-d') ?? '') ?>" required>
                <input type="text" name="class_name" placeholder="Classe" value="<?= e($this->student?->get_class_name() ?? '') ?>" required>
                <input type="text" name="city" placeholder="Comune" value="<?= e($this->student?->get_city() ?? '') ?>" required>

                <select name="study_type" required>
                    <option value="">Programmazione</option>
                    <option value="differenziata" <?= ($this->student?->get_study_type()?->value ?? '') === 'differenziata' ? 'selected' : '' ?>>differenziata</option>
                    <option value="obiettivi minimi" <?= ($this->student?->get_study_type()?->value ?? '') === 'obiettivi minimi' ? 'selected' : '' ?>>obiettivi minimi</option>
                </select>

                <input type="number" name="hours" min="1" placeholder="Ore" value="<?= e((string) ($this->student?->get_hours() ?? '')) ?>" required>

                <select name="teacher_ids[]" multiple>
                    <option value="">Docenti di sostegno</option>
                    <?php foreach ($this->teachers as $teacher): ?>
                        <option value="<?= e((string) $teacher->get_id()) ?>"><?= e($teacher->get_last_name() . ' ' . $teacher->get_first_name()) ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="tutor_ids[]" multiple>
                    <option value="">Tutor</option>
                    <?php foreach ($this->tutors as $tutor): ?>
                        <option value="<?= e((string) $tutor->get_id()) ?>"><?= e($tutor->get_last_name() . ' ' . $tutor->get_first_name()) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit"><?= e($is_update ? 'Aggiorna' : 'Crea') ?></button>
            </form>
        </section>
    <?php }
}