<?php

namespace App\Components;

use Camezilla\Components\Component;

class TutorGroup extends Component {

    public function __construct(private array $tutors, private array $cooperatives = []) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="page-card">
            <div class="page-card__header">
                <div>
                    <h1>Tutor</h1>
                    <p>Figure esterne collegate alle cooperative.</p>
                </div>
            </div>

            <?= new TutorForm(null, $this->cooperatives) ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cooperativa</th>
                            <th>Cellulare</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->tutors)): ?>
                            <tr><td colspan="3">Nessun tutor trovato.</td></tr>
                        <?php else: ?>
                            <?php foreach ($this->tutors as $tutor): ?>
                                <?= new TutorItem($tutor, $this->cooperatives[$tutor->get_cooperative_id()] ?? '') ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php }
}