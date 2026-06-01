<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$iniziali_utente = isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : "U";
$nome_cognome_utente = isset($_SESSION['email']) ? htmlspecialchars(explode('@', $_SESSION['email'])[0]) : "Ospite";
$ruolo_utente = "Docente Autorizzato";

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$cooperatives = [];
$coopResult = $conn->query("SELECT id, name FROM cooperatives ORDER BY name ASC");
if ($coopResult) {
    while ($row = $coopResult->fetch_assoc()) {
        $cooperatives[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Tutor - Gestione Sostegno</title>
    <link rel="stylesheet" href="style.css">
    <script src="../js/general.js" defer></script>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
                <div class="topbar-left">
                    <div class="topbar-title">Nuovo Tutor</div>
                </div>
                <div class="user-profile">
                    <div class="avatar"><?php echo $iniziali_utente; ?></div>
                    <div class="user-info">
                        <strong><?php echo $nome_cognome_utente; ?></strong>
                        <span><?php echo $ruolo_utente; ?></span>
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
                        <h1>Aggiungi Nuovo Tutor</h1>
                        <p>Inserisci i dati del professionista per registrarlo nel sistema.</p>
                    </div>
                </div>

                <form action="salva_tutor.php" method="POST" class="add-form">
                    
                    <div class="form-section-card">
                        <div class="section-header">
                            <span class="section-icon">📝</span>
                            <h2>Dati Anagrafici</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome <span class="required">*</span></label>
                                <input type="text" id="nome" name="nome" placeholder="Es. Giulia" required>
                            </div>
                            <div class="form-group">
                                <label for="cognome">Cognome <span class="required">*</span></label>
                                <input type="text" id="cognome" name="cognome" placeholder="Es. Bianchi" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="codice_fiscale">Codice Fiscale</label>
                                <input type="text" id="codice_fiscale" name="codice_fiscale" placeholder="16 caratteri" maxlength="16" style="text-transform: uppercase;">
                            </div>
                            <div class="form-group">
                                <label for="data_nascita">Data di Nascita</label>
                                <input type="date" id="data_nascita" name="data_nascita">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <div class="section-header">
                            <span class="section-icon">📞</span>
                            <h2>Recapiti e Contatti</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono">Numero di Telefono <span class="required">*</span></label>
                                <input type="text" id="telefono" name="telefono" placeholder="Es. +39 345 1234567" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <div class="section-header">
                            <span class="section-icon">🏢</span>
                            <h2>Inquadramento Professionale</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cooperativa">Ente di provenienza <span class="required">*</span></label>
                                <select id="cooperativa" name="cooperativa" required>
                                    <option value="" disabled selected>Seleziona la cooperativa...</option>
                                    <?php foreach ($cooperatives as $coop): ?>
                                        <option value="<?php echo $coop['id']; ?>"><?php echo htmlspecialchars($coop['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="monte_ore">Monte Ore Settimanale</label>
                                <div class="input-with-suffix">
                                    <input type="number" id="monte_ore" name="monte_ore" min="1" max="40" placeholder="Es. 18">
                                    <span class="suffix">ore / sett.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="note">Note aggiuntive</label>
                            <textarea id="note" name="note" rows="4" placeholder="Inserisci eventuali note o qualifiche specifiche..." style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem; outline: none; background-color: white; resize: vertical;"></textarea>
                        </div>
                    </div>
                
                    <div class="form-actions">
                        <a href="tutor.php" class="btn-cancel">Annulla</a>
                        <button type="submit" class="btn-primary">Salva Tutor</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>