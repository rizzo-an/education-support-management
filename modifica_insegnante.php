<?php
session_start();

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$errore = "";
$teacher = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$teacher) {
    die("Insegnante non trovato o ID mancante nell'URL.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contract_type = trim($_POST['contract_type'] ?? 'Tempo Indeterminato');
    $area = trim($_POST['area_specializzazione'] ?? 'Nessuna specifica');

    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
        $sql = "UPDATE teachers SET first_name = ?, last_name = ?, email = ?, contract_type = ?, area_specializzazione = ? WHERE id = ?";
        $stmt_up = $conn->prepare($sql);
        if ($stmt_up) {
            $stmt_up->bind_param("sssssi", $first_name, $last_name, $email, $contract_type, $area, $id);
            if ($stmt_up->execute()) {
                header("Location: insegnanti.php?aggiornato=1");
                exit;
            } else {
                $errore = "Errore durante l'aggiornamento dei dati.";
            }
            $stmt_up->close();
        }
    } else {
        $errore = "Tutti i campi sono obbligatori.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Insegnante - Gestione Sostegno</title>
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
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Modifica Insegnante</div>
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
                        <h1>Modifica Scheda Insegnante</h1>
                        <p>Stai aggiornando il profilo del docente: <strong><?php echo htmlspecialchars($teacher['first_name'] . " " . $teacher['last_name']); ?></strong></p>
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
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Cognome <span style="color: #dc2626;">*</span></label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                        </div>

                        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Email Istituzionale <span style="color: #dc2626;">*</span></label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;" for="contract_type">Tipo di Contratto</label>
                                <select id="contract_type" name="contract_type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;">
                                    <option value="Tempo Indeterminato" <?php if(($teacher['contract_type'] ?? '') === 'Tempo Indeterminato') echo 'selected'; ?>>Di Ruolo (Indeterminato)</option>
                                    <option value="Tempo Determinato" <?php if(($teacher['contract_type'] ?? '') === 'Tempo Determinato') echo 'selected'; ?>>Organico di Fatto (Determinato)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;" for="area_specializzazione">Area di Specializzazione</label>
                            <select id="area_specializzazione" name="area_specializzazione" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;">
                                <option value="Nessuna specifica" <?php if(($teacher['area_specializzazione'] ?? '') === 'Nessuna specifica') echo 'selected'; ?>>Nessuna specifica</option>
                                <option value="Area Umanistica (AD04)" <?php if(($teacher['area_specializzazione'] ?? '') === 'Area Umanistica (AD04)') echo 'selected'; ?>>Area Umanistica (AD04)</option>
                                <option value="Area Scientifica (AD01)" <?php if(($teacher['area_specializzazione'] ?? '') === 'Area Scientifica (AD01)') echo 'selected'; ?>>Area Scientifica (AD01)</option>
                                <option value="Area Tecnica (AD03)" <?php if(($teacher['area_specializzazione'] ?? '') === 'Area Tecnica (AD03)') echo 'selected'; ?>>Area Tecnica (AD03)</option>
                            </select>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                            <a href="insegnanti.php" class="btn-outline" style="padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 500;">Annulla</a>
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