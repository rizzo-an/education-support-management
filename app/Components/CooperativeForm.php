<?php

namespace App\Components;

use App\Models\Cooperative;
use Camezilla\Components\Component;

class CooperativeForm extends Component {

    public function __construct(private ?Cooperative $cooperative = null) {
        parent::__construct();
    }

    protected function build(): void {
        $is_update = $this->cooperative !== null;
        $action = $is_update ? 'update' : 'create';
        ?>
        <section class="form-card">
            <h2><?= e($is_update ? 'Modifica cooperativa' : 'Nuova cooperativa') ?></h2>

            <form class="entity-form" action="<?= get_absolute_url('actions/cooperative.php?path=' . $action . '&redirect=cooperatives.php') ?>" method="post">
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?= e((string) $this->cooperative?->get_id()) ?>">
                <?php endif; ?>

                <input type="text" name="name" placeholder="Nome cooperativa" value="<?= e($this->cooperative?->get_name() ?? '') ?>" required>
                <button type="submit"><?= e($is_update ? 'Aggiorna' : 'Crea') ?></button>
            </form>
        </section>
    <?php }
}