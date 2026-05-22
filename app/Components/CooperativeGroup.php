<?php

namespace App\Components;

use Camezilla\Components\Component;

class CooperativeGroup extends Component {

    public function __construct(private array $cooperatives) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <section class="page-card">
            <div class="page-card__header">
                <div>
                    <h1>Cooperative</h1>
                    <p>Cooperative collegate ai tutor esterni.</p>
                </div>
            </div>

            <?= new CooperativeForm() ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($this->cooperatives)): ?>
                            <tr><td>Nessuna cooperativa trovata.</td></tr>
                        <?php else: ?>
                            <?php foreach ($this->cooperatives as $cooperative): ?>
                                <?= new CooperativeItem($cooperative) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php }
}