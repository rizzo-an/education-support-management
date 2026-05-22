<?php

namespace App\Components;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tutor;
use Camezilla\Components\Component;

class StudentGroup extends Component {

    public function __construct(
        private array $students,
        private array $filters = [],
        private array $teachers = [],
        private array $tutors = []
    ) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="page-card">
            <div class="page-card__header">
                <div>
                    <h1>Studenti</h1>
                    <p>Elenco filtrabile in stile foglio dati.</p>
                </div>
                <div class="page-card__meta"><?= count($this->students) ?> record</div>
            </div>

            <?= new StudentForm(null, $this->teachers, $this->tutors) ?>

            <form class="filters-form" method="get" action="<?= page('index.php') ?>">
                <input type="text" name="first_name" placeholder="Nome" value="<?= e((string) ($this->filters['first_name'] ?? '')) ?>">
                <input type="text" name="last_name" placeholder="Cognome" value="<?= e((string) ($this->filters['last_name'] ?? '')) ?>">
                <input type="text" name="class_name" placeholder="Classe" value="<?= e((string) ($this->filters['class_name'] ?? '')) ?>">
                <input type="text" name="city" placeholder="Comune" value="<?= e((string) ($this->filters['city'] ?? '')) ?>">
                <select name="study_type">
                    <option value="">Programmazione</option>
                    <option value="differenziata" <?= (($this->filters['study_type'] ?? '') === 'differenziata') ? 'selected' : '' ?>>differenziata</option>
                    <option value="obiettivi minimi" <?= (($this->filters['study_type'] ?? '') === 'obiettivi minimi') ? 'selected' : '' ?>>obiettivi minimi</option>
                </select>
                <input type="number" name="hours" min="1" placeholder="Ore" value="<?= e((string) ($this->filters['hours'] ?? '')) ?>">
                <select name="teacher_id">
                    <option value="">Docente</option>
                    <?php foreach ($this->teachers as $teacher): ?>
                        <option value="<?= e((string) $teacher->get_id()) ?>" <?= ((string) ($this->filters['teacher_id'] ?? '') === (string) $teacher->get_id()) ? 'selected' : '' ?>><?= e($teacher->get_last_name() . ' ' . $teacher->get_first_name()) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tutor_id">
                    <option value="">Tutor</option>
                    <?php foreach ($this->tutors as $tutor): ?>
                        <option value="<?= e((string) $tutor->get_id()) ?>" <?= ((string) ($this->filters['tutor_id'] ?? '') === (string) $tutor->get_id()) ? 'selected' : '' ?>><?= e($tutor->get_last_name() . ' ' . $tutor->get_first_name()) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="filters-form__actions">
                    <button type="submit">Filtra</button>
                    <a class="button-ghost" href="<?= page('index.php') ?>">Reset</a>
                </div>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Studente</th>
                            <th>Classe</th>
                            <th>Comune</th>
                            <th>Programmazione</th>
                            <th>Ore</th>
                            <th>Docenti</th>
                            <th>Tutor</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->students)): ?>
                            <tr>
                                <td colspan="8">Nessuno studente trovato.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($this->students as $student): ?>
                                <?= new StudentItem($student) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php }
}