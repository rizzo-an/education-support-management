<?php

namespace App\Components;

use App\Models\Teacher;
use Camezilla\Components\Component;

class TeacherForm extends Component {

    public function __construct(private ?Teacher $teacher = null) {
        parent::__construct();
    }

    protected function build(): void {
        $is_update = $this->teacher !== null;
        $action = $is_update ? 'update' : 'create';
        ?>
        <section class="form-card">
            <h2><?= e($is_update ? 'Modifica docente' : 'Nuovo docente') ?></h2>

            <form class="entity-form" action="<?= get_absolute_url('actions/teacher.php?path=' . $action . '&redirect=teachers.php') ?>" method="post">
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?= e((string) $this->teacher?->get_id()) ?>">
                <?php endif; ?>

                <input type="text" name="first_name" placeholder="Nome" value="<?= e($this->teacher?->get_first_name() ?? '') ?>" required>
                <input type="text" name="last_name" placeholder="Cognome" value="<?= e($this->teacher?->get_last_name() ?? '') ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= e($this->teacher?->get_email() ?? '') ?>" required>
                <button type="submit"><?= e($is_update ? 'Aggiorna' : 'Crea') ?></button>
            </form>
        </section>
    <?php }
}