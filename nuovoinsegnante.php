<?php
session_start();
$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
$errore = "";
$successo = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contract_type = trim($_POST['contract_type'] ?? 'Tempo Indeterminato'); 
    $area = trim($_POST['area_specializzazione'] ?? 'Nessuna specifica');

    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
        $sql = "INSERT INTO teachers (first_name, last_name, email, contract_type, area_specializzazione) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $contract_type, $area);
            if ($stmt->execute()) {
                header("Location: insegnanti.php?successo=1");
                exit;
            } else {
                $errore = "Errore durante il salvataggio dell'insegnante nel database.";
            }
            $stmt->close();
        }
    } else {
        $errore = "I campi Nome, Cognome e Email sono obbligatori.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Insegnante - Gestione Sostegno</title>
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
                <a href="insegnanti.php" class="nav-item active">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
                <a href="cooperative.php" class="nav-item">
                    <span class="icon">🏢</span> Cooperative
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="cookie-btn">Manage cookies or opt out</button>
            </div>
        </aside>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-title">Nuovo Insegnante</div>
                <div class="user-profile">
                        <div class="avatar">
                            <?php 
                            echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : "U"; 
                            ?>
                        </div>
                        <div class="user-info">
                            <strong>
                                <?php 
                                if (isset($_SESSION['email'])) {
                                    echo htmlspecialchars(explode('@', $_SESSION['email'])[0]);
                                } else {
                                    echo "Ospite";
                                }
                                ?>
                            </strong>
                            <span>Docente Autorizzato</span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="logout.php" style="font-size: 0.8rem; color: #dc2626; text-decoration: none; display: block; margin-top: 2px;">Disconnetti</a>
                            <?php endif; ?>
                        </div>
                    </div>
            </header>

            <main class="content">
                <a href="insegnanti.php" class="back-link">← Torna all'elenco Insegnanti</a>
                <div class="page-header">
                    <div>
                        <h1>Nuovo Insegnante</h1>
                        <p>Inserisci un nuovo docente nell'organico di sostegno dell'istituto.</p>
                    </div>
                </div>

                <?php if (!empty($errore)): ?>
                    <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                        <span style="color: #991b1b; font-weight: bold;">⚠️ Errore: </span>
                        <span style="color: #991b1b;"><?php echo $errore; ?></span>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <form action="" method="POST">
                        <div class="section-group">
                            <h3>Dati Anagrafici</h3>
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input type="text" name="first_name" required placeholder="Es. Mario">
                                </div>
                                <div class="form-group">
                                    <label>Cognome</label>
                                    <input type="text" name="last_name" required placeholder="Es. Rossi">
                                </div>
                                <div class="form-group">
                                    <label>Email Istituzionale</label>
                                    <input type="email" name="email" required placeholder="mario.rossi@scuola.edu.it">
                                </div>
                            </div>
                        </div>

                        <div class="section-group" style="margin-top: 24px;">
                            <h3>Inquadramento e Ruolo</h3>
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label for="contract_type">Tipo di Contratto</label>
                                    <select id="contract_type" name="contract_type" required>
                                        <option value="Tempo Indeterminato">Di Ruolo (Indeterminato)</option>
                                        <option value="Tempo Determinato">Organico di Fatto (Determinato)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Area di Specializzazione</label>
                                    <select name="area_specializzazione" required>
                                        <option value="Nessuna specifica">Nessuna specifica</option>
                                        <option value="Area Umanistica (AD04)">Area Umanistica (AD04)</option>
                                        <option value="Area Scientifica (AD01)">Area Scientifica (AD01)</option>
                                        <option value="Area Tecnica (AD03)">Area Tecnica (AD03)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions-footer">
                            <a href="insegnanti.php" class="btn-text">Annulla</a>
                            <button type="submit" class="btn-primary-blue" style="width:auto; padding: 10px 16px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Salva Insegnante
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>