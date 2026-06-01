<?php
session_start();

if (!isset($_GET['id'])) {
    die('ID cooperativa non valido.');
}

$id = intval($_GET['id']);
$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$errore = "";

$stmt = $conn->prepare("SELECT * FROM cooperatives WHERE id = ? LIMIT 1");
if (!$stmt) {
    die('Errore nella query.');
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die('Cooperativa non trovata.');
}
$cooperativa = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $errore = "Il nome della cooperativa è obbligatorio.";
    } else {
        $update = $conn->prepare("UPDATE cooperatives SET name = ? WHERE id = ?");
        if ($update) {
            $update->bind_param("si", $name, $id);
            if ($update->execute()) {
                $update->close();
                $conn->close();
                header("Location: cooperative.php");
                exit;
            }
            $update->close();
        }
        $errore = "Errore durante l'aggiornamento della cooperativa.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Cooperativa - Gestione Sostegno</title>
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
                <a href="cooperative.php" class="nav-item active">
                    <span class="icon">🏢</span> Cooperative
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Modifica Cooperativa</div>
                <div class="user-profile">
                    <div class="avatar"><?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'U'; ?></div>
                    <div class="user-info">
                        <strong><?php echo isset($_SESSION['email']) ? htmlspecialchars(explode('@', $_SESSION['email'])[0]) : 'Ospite'; ?></strong>
                        <span>Docente Autorizzato</span>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="logout.php" style="font-size: 0.8rem; color: #dc2626; text-decoration: none; display: block; margin-top: 2px;">Disconnetti</a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <main class="content">
                <a href="cooperative.php" class="back-link">← Torna all'elenco Cooperative</a>
                <div class="page-header">
                    <div>
                        <h1>Modifica Cooperativa</h1>
                        <p>Aggiorna il nome della cooperativa selezionata.</p>
                    </div>
                </div>

                <?php if ($errore): ?>
                    <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                        <strong style="color: #991b1b;">Errore:</strong> <?php echo htmlspecialchars($errore); ?>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="name">Nome Cooperativa <span style="color: #dc2626;">*</span></label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? $cooperativa['name']); ?>" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
                        </div>
                        <div class="form-actions-footer" style="margin-top: 24px; gap: 12px; display: flex; justify-content: flex-end;">
                            <a href="cooperative.php" class="btn-outline">Annulla</a>
                            <button type="submit" class="btn-primary-blue" style="width:auto; padding: 10px 16px;">Salva Modifiche</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
