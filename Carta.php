<?php
$iniziali_utente = "UT"; 
$nome_cognome_utente = "Utente di Prova";
$ruolo_utente = "Docente";

$lista_tutor = [
    [
        "nome" => "Giulia Bianchi",
        "ruolo" => "TUTOR 1:1",
        "coop" => "Coop. L'Albero della Vita",
        "telefono" => "+39 345 1234567"
    ],
    [
        "nome" => "Alessandro Neri",
        "ruolo" => "TUTOR 1:1",
        "coop" => "Ente Terzo Settore Milano",
        "telefono" => "+39 333 9876543"
    ],
    [
        "nome" => "Martina Rossi",
        "ruolo" => "ASS. COMUNICAZIONE",
        "coop" => "Cooperativa Sociale ABC",
        "telefono" => "+39 328 1122334"
    ] 
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Sostegno - Tutor</title>
    <link rel="stylesheet" href="Carta.css">
</head>
<body>
                <div class="cards-grid" id="tutorGrid">
                    <?php foreach ($lista_tutor as $tutor): ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-icon">👤</div>
                                <span class="badge"><?php echo htmlspecialchars($tutor['ruolo']); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($tutor['nome']); ?></h3>
                            <p class="coop"><?php echo htmlspecialchars($tutor['coop']); ?></p>
                            <div class="phone"><span>📞</span> <?php echo htmlspecialchars($tutor['telefono']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>
    </div>
</body>
</html>