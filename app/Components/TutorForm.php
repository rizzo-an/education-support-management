<?php

namespace App\Components;

use App\Models\Cooperative;
use App\Models\Tutor;
use Camezilla\Components\Component;

class TutorForm extends Component {

    public function __construct(private ?Tutor $tutor = null, private array $cooperatives = []) {
        parent::__construct();
    }

    protected function build(): void {
        $is_update = $this->tutor !== null;
        $action = $is_update ? 'update' : 'create';
        ?>
        <section class="form-card">
            <h2><?= e($is_update ? 'Modifica tutor' : 'Nuovo tutor') ?></h2>

            <form class="entity-form" action="<?= get_absolute_url('actions/tutor.php?path=' . $action . '&redirect=tutors.php') ?>" method="post">
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?= e((string) $this->tutor?->get_id()) ?>">
                <?php endif; ?>

                <select name="cooperative_id" required>
                    <option value="">Cooperativa</option>
                    <?php foreach ($this->cooperatives as $cooperative): ?>
                        <option value="<?= e((string) $cooperative->get_id()) ?>" <?= ($this->tutor?->get_cooperative_id() ?? '') == $cooperative->get_id() ? 'selected' : '' ?>><?= e($cooperative->get_name() ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="first_name" placeholder="Nome" value="<?= e($this->tutor?->get_first_name() ?? '') ?>" required>
                <input type="text" name="last_name" placeholder="Cognome" value="<?= e($this->tutor?->get_last_name() ?? '') ?>" required>
                <input type="text" name="telephone_number" placeholder="Cellulare" value="<?= e($this->tutor?->get_telephone_number() ?? '') ?>" required>
                <button type="submit"><?= e($is_update ? 'Aggiorna' : 'Crea') ?></button>
            </form>
        </section>
    <?php }
}