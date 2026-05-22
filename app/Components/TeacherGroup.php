<?php

namespace App\Components;

use Camezilla\Components\Component;

class TeacherGroup extends Component {

    public function __construct(private array $teachers) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="page-card">
            <div class="page-card__header">
                <div>
                    <h1>Docenti di sostegno</h1>
                    <p>Docenti interni con dominio istituzionale.</p>
                </div>
            </div>

            <?= new TeacherForm() ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->teachers)): ?>
                            <tr><td colspan="2">Nessun docente trovato.</td></tr>
                        <?php else: ?>
                            <?php foreach ($this->teachers as $teacher): ?>
                                <?= new TeacherItem($teacher) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php }
}