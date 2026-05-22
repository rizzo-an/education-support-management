<?php

namespace App\Components;

use Camezilla\Components\Component;

class Navbar extends Component {

    protected function build(): void { ?>
        <nav class="filter-menu">
            <div class="header-container menu-wrapper">
                <div class="brand-block">
                    <div class="brand-title">Archivio di sostegno</div>
                    <div class="brand-subtitle">Studenti, docenti, tutor e cooperative</div>
                </div>

                <ul id="nav-links">
                    <li><a href="<?= page('index.php') ?>" class="active"><i class="fas fa-home"></i> Studenti</a></li>
                    <li><a href="<?= page('teachers.php') ?>"><i class="fas fa-chalkboard-teacher"></i> Docenti</a></li>
                    <li><a href="<?= page('tutors.php') ?>"><i class="fas fa-user-friends"></i> Tutor</a></li>
                    <li><a href="<?= page('cooperatives.php') ?>"><i class="fas fa-building"></i> Cooperative</a></li>
                    <li><a href="<?= page('users.php') ?>"><i class="fas fa-users"></i> Utenti</a></li>
                    <?php if (is_user_authenticated()): ?>
                        <li><a href="<?= get_absolute_url('actions/account.php?path=logout') ?>"><i class="fas fa-right-from-bracket"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= page('login.php') ?>"><i class="fas fa-right-to-bracket"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    <?php }
}