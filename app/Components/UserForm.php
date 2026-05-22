<?php

namespace App\Components;

use App\Models\User;
use Camezilla\Components\Component;

class UserForm extends Component {

    public function __construct(private ?User $user = null) {
        parent::__construct();
    }

    protected function build(): void {
        $is_update = $this->user !== null;
        $action = $is_update ? 'update' : 'create';
        ?>
        <section class="form-card">
            <h2><?= e($is_update ? 'Modifica utente' : 'Nuovo utente') ?></h2>

            <form class="entity-form" action="<?= get_absolute_url('actions/user.php?path=' . $action . '&redirect=users.php') ?>" method="post">
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?= e((string) $this->user?->get_id()) ?>">
                <?php endif; ?>

                <input type="email" name="email" placeholder="Email" value="<?= e($this->user?->get_email() ?? '') ?>" required>
                <input type="password" name="password" placeholder="Password" value="" required>
                <button type="submit"><?= e($is_update ? 'Aggiorna' : 'Crea') ?></button>
            </form>
        </section>
    <?php }
}