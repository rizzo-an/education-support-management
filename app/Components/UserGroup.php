<?php

namespace App\Components;

use Camezilla\Components\Component;

class UserGroup extends Component {

    public function __construct(private array $users) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="page-card">
            <div class="page-card__header">
                <div>
                    <h1>Utenti</h1>
                    <p>Accessi riservati al personale autorizzato.</p>
                </div>
            </div>

            <?= new UserForm() ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->users)): ?>
                            <tr><td>Nessun utente trovato.</td></tr>
                        <?php else: ?>
                            <?php foreach ($this->users as $user): ?>
                                <?= new UserItem($user) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php }
}