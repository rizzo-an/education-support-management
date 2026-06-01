<?php
session_start();

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$seeded = false;
$errorMessage = '';
if (isset($_GET['error']) && $_GET['error'] === '1') {
    $errorMessage = 'Impossibile eliminare la cooperativa perché è ancora assegnata a uno o più tutor.';
}
$coopQuery = "SELECT * FROM cooperatives ORDER BY name ASC";
$result = $conn->query($coopQuery);
if ($result && $result->num_rows === 0) {
    $seedList = [
        'Cooperativa Arcobaleno',
        'Cooperativa Aurora',
        'Cooperativa Sostegno Insieme'
    ];
    $stmt = $conn->prepare("INSERT INTO cooperatives (name) VALUES (?)");
    if ($stmt) {
        foreach ($seedList as $seedName) {
            $stmt->bind_param("s", $seedName);
            $stmt->execute();
        }
        $stmt->close();
        $seeded = true;
    }
    $result = $conn->query($coopQuery);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Cooperative - Gestione Sostegno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="logo">
                <span class="logo-icon">👥</span>
                <h2>GestioneSostegno</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="studenti.php" class="nav-item">
                    <span class="icon">🪟</span> Studenti
                </a>
                <a href="tutor.php" class="nav-item">
                    <span class="icon">👤</span> Tutor
                </a>
                <a href="insegnanti.php" class="nav-item">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
                <a href="#" class="nav-item active">
                    <span class="icon">🏢</span> Cooperative
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="cookie-btn">Gestisci Cookie</button>
            </div>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Cooperative</div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="user-profile">
                        <div class="avatar">
                            <?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'U'; ?>
                        </div>
                        <div class="user-info">
                            <strong>
                                <?php echo isset($_SESSION['email']) ? htmlspecialchars(explode('@', $_SESSION['email'])[0]) : 'Ospite'; ?>
                            </strong>
                            <span>Docente Autorizzato</span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="logout.php" style="font-size: 0.8rem; color: #dc2626; text-decoration: none; display: block; margin-top: 2px;">Disconnetti</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content">
                <div class="page-header">
                    <div>
                        <h1>Cooperative</h1>
                        <p>Gestisci le cooperative disponibili per i tutor e mantieni aggiornato l'elenco.</p>
                    </div>
                    <a href="nuova_cooperativa.php">
                        <button class="btn-primary">+ Nuova Cooperativa</button>
                    </a>
                </div>

                <?php if ($seeded): ?>
                    <div class="alert-box" style="border-left: 4px solid #0f766e; background: #ecfdf5; padding: 15px; margin-bottom: 20px; border-radius: 6px; color: #115e59;">
                        Sono state aggiunte cooperative di esempio perché la tabella era vuota.
                    </div>
                <?php endif; ?>
                <?php if ($errorMessage): ?>
                    <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2; padding: 15px; margin-bottom: 20px; border-radius: 6px; color: #991b1b;">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="cards-grid">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $id = $row['id'];
                                $nome = htmlspecialchars($row['name']);
                            ?>
                            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 180px;">
                                <div>
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 12h18"></path>
                                                <path d="M12 3v18"></path>
                                            </svg>
                                        </div>
                                        <span class="badge-green">COOPERATIVA</span>
                                    </div>
                                    <h3><?php echo $nome; ?></h3>
                                    <p class="coop">Cooperativa di supporto educativo</p>
                                </div>

                                <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                                    <a href="modifica_cooperativa.php?id=<?php echo $id; ?>" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block;">Modifica</a>
                                    <a href="elimina_cooperativa.php?id=<?php echo $id; ?>" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block; color: #dc2626; border-color: #fca5a5;" onclick="return confirm('Sei sicuro di voler eliminare la cooperativa <?php echo addcslashes($nome, "'\\"); ?>?');">Elimina</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: span 3; text-align: center; color: #64748b; padding: 20px;">Ancora nessuna cooperativa presente nel database.</p>
                    <?php endif; ?>
                    <?php $conn->close(); ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
