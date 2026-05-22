<?php

namespace App\Components;

use Camezilla\Components\Component;

class Login extends Component {

    protected function build(): void { ?>
        <section class="auth-card">
            <div class="auth-card__header">
                <h1>Accesso riservato</h1>
                <p>Inserisci le credenziali istituzionali per entrare nell'archivio.</p>
            </div>

            <form class="auth-form" action="<?= get_absolute_url('actions/account.php?path=login&redirect=index.php') ?>" method="post">
                <label>
                    Email
                    <input type="email" name="email" required>
                </label>

                <label>
                    Password
                    <input type="password" name="password" required>
                </label>

                <button type="submit">Entra</button>
            </form>
        </section>
    <?php }
}