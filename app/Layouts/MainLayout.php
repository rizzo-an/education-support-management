<?php

namespace App\Layouts;

use App\Components\Navbar;
use Camezilla\Layouts\Layout;

class MainLayout extends Layout {

    public function __construct(string $title) {
        parent::__construct($title);
    }

    protected function build(): void { ?>
        <!DOCTYPE html>
        <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="<?= resource('css/style.css') ?>">
                <title><?= e($this->title) ?></title>
            </head>
            <body class="body">
                <?= new Navbar() ?>

                <main class="main">
                    <?php $this->render_content(); ?>
                </main>
            </body>
        </html>
    <?php }
}