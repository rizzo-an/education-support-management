<?php

namespace App\Components;

use App\Models\User;
use Camezilla\Components\Component;

class UserItem extends Component {

    public function __construct(private User $user) {
        parent::__construct();
    }

    protected function build(): void { ?>
        <tr>
            <td><?= e((string) $this->user->get_email()) ?></td>
        </tr>
    <?php }
}