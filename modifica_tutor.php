<?php
session_start();

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$errore = "";
$tutor = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $tutor = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$tutor) {
    die("Errore: Tutor non trovato o ID mancante nell'URL.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['nome'] ?? '');
    $last_name = trim($_POST['cognome'] ?? '');
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $birth_date = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : null;
    $telephone_number = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cooperative_id = !empty($_POST['cooperativa']) ? intval($_POST['cooperativa']) : null;
    $monte_ore = !empty($_POST['monte_ore']) ? intval($_POST['monte_ore']) : null;
    $note = trim($_POST['note'] ?? '');

    if (!empty($first_name) && !empty($last_name) && !empty($telephone_number) && !empty($email)) {
        $sql = "UPDATE tutors SET first_name = ?, last_name = ?, codice_fiscale = ?, birth_date = ?, telephone_number = ?, email = ?, cooperative_id = ?, monte_ore = ?, note = ? WHERE id = ?";
        $stmt_up = $conn->prepare($sql);
        if ($stmt_up) {
            $stmt_up->bind_param("ssssssissi", $first_name, $last_name, $codice_fiscale, $birth_date, $telephone_number, $email, $cooperative_id, $monte_ore, $note, $id);
            if ($stmt_up->execute()) {
                header("Location: tutor.php?aggiornato=1");
                exit;
            } else {
                $errore = "Errore durante l'aggiornamento dei dati nel database.";
            }
            $stmt_up->close();
        }
    } else {
        $errore = "Tutti i campi principali (Nome, Cognome, Telefono e Email) sono obbligatori.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Tutor - Gestione Sostegno</title>
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
                <a href="tutor.php" class="nav-item active">
                    <span class="icon">👤</span> Tutor
                </a>
                <a href="insegnanti.php" class="nav-item">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
                <a href="cooperative.php" class="nav-item">
                    <span class="icon">🏢</span> Cooperative
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Modifica Tutor</div>
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
                <a href="tutor.php" class="back-link">← Torna all'elenco Tutor</a>
                <div class="page-header">
                    <div>
                        <h1>Modifica Scheda Tutor</h1>
                        <p>Stai modificando i dati esistenti di: <strong><?php echo htmlspecialchars($tutor['first_name'] . " " . $tutor['last_name']); ?></strong></p>
                    </div>
                </div>

                <?php if (!empty($errore)): ?>
                    <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
                        <span style="color: #991b1b; font-weight: bold;">⚠️ Errore: </span>
                        <span style="color: #991b1b;"><?php echo $errore; ?></span>
                    </div>
                <?php endif; ?>

                <div class="table-card" style="padding: 30px;">
                    <form action="" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Nome <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="nome" value="<?php echo htmlspecialchars($tutor['first_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Cognome <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="cognome" value="<?php echo htmlspecialchars($tutor['last_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Codice Fiscale</label>
                                <input type="text" name="codice_fiscale" maxlength="16" value="<?php echo htmlspecialchars($tutor['codice_fiscale'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; text-transform: uppercase;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Data di Nascita</label>
                                <input type="date" name="data_nascita" value="<?php echo htmlspecialchars($tutor['birth_date'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Numero di Telefono <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="telefono" value="<?php echo htmlspecialchars($tutor['telephone_number']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Email <span style="color: #dc2626;">*</span></label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($tutor['email'] ?? ''); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;\">Monte Ore Settimanale</label>
                                <input type="number" name="monte_ore" min="1" max="40" value="<?php echo htmlspecialchars($tutor['monte_ore'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                            <div style="display: none;"></div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Cooperativa Assegnata</label>
                                <select name="cooperativa" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: white;">
                                    <option value="">Nessuna cooperativa</option>
                                    <?php
                                    $coop_result = $conn->query("SELECT id, name FROM cooperatives ORDER BY name ASC");
                                    if ($coop_result && $coop_result->num_rows > 0) {
                                        while($coop = $coop_result->fetch_assoc()) {
                                            $selected = ($coop['id'] == $tutor['cooperative_id']) ? "selected" : "";
                                            echo '<option value="' . $coop['id'] . '" ' . $selected . '>' . htmlspecialchars($coop['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Note aggiuntive</label>
                            <textarea name="note" rows="4" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; resize: vertical;"><?php echo htmlspecialchars($tutor['note'] ?? ''); ?></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                            <a href="tutor.php" class="btn-outline" style="padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 500;">Annulla</a>
                            <button type="submit" class="btn-primary" style="padding: 10px 20px; border-radius: 6px; font-weight: 500; cursor: pointer;">Salva Modifiche</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>