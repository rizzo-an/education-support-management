<?php
session_start();
$iniziali_utente = isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 2)) : "AD";
$nome_cognome_utente = isset($_SESSION['email']) ? htmlspecialchars(explode('@', $_SESSION['email'])[0]) : "Amministratore";
$ruolo_utente = isset($_SESSION['role']) ? $_SESSION['role'] : "Admin";
// carica tutor e insegnanti per i controlli del form
$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
$tutors = [];
$teachers = [];
if (!$conn->connect_error) {
    $res = $conn->query("SELECT id, first_name, last_name FROM tutors ORDER BY last_name ASC");
    if ($res) {
        while ($r = $res->fetch_assoc()) $tutors[] = $r;
    }
    $res2 = $conn->query("SELECT id, first_name, last_name FROM teachers ORDER BY last_name ASC");
    if ($res2) {
        while ($r = $res2->fetch_assoc()) $teachers[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Studente - Gestione Sostegno</title>
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
                <a href="studenti.php" class="nav-item active">
                    <span class="icon">🪟</span> Studenti
                </a>
                <a href="tutor.php" class="nav-item">
                    <span class="icon">👤</span> Tutor
                </a>
                <a href="insegnanti.php" class="nav-item">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-title">Dashboard</div>
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
                <a href="studenti.php" class="back-link">← Torna all'elenco Studenti</a>
                
                <div class="page-header">
                    <div>
                        <h1>Aggiungi Nuovo Studente</h1>
                        <p>Inserisci i dati anagrafici e il piano educativo dello studente.</p>
                    </div>
                </div>

                <form action="salva_studente.php" method="POST" class="add-form">

                        <div class="section-header">
                            <span class="section-icon">👤</span>
                            <h2>Dati Studente</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome <span class="required">*</span></label>
                                <input type="text" id="nome" name="nome" placeholder="Es. Mario" required>
                            </div>
                            <div class="form-group">
                                <label for="cognome">Cognome <span class="required">*</span></label>
                                <input type="text" id="cognome" name="cognome" placeholder="Es. Rossi" required>
                            </div>
                            <div class="form-row">
    <div class="form-group">
        <label for="data_nascita">Data di Nascita <span class="required">*</span></label>
        <input type="date" id="data_nascita" name="data_nascita" required>
    </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="comune">Comune di Appartenenza <span class="required">*</span></label>
                                <input type="text" id="comune" name="comune" placeholder="Es. Milano" required>
                            </div>
                            <div class="form-group">
                                <label for="classe">Classe <span class="required">*</span></label>
                                <input type="text" id="classe" name="classe" placeholder="Es. 3A" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <div class="section-header">
                            <span class="section-icon">📄</span>
                            <h2>Dettagli Programmazione</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_programmazione">Tipo di Programmazione <span class="required">*</span></label>
                                <select id="tipo_programmazione" name="tipo_programmazione" required>
                                    <option value="" disabled selected>Seleziona tipologia...</option>
                                    <option value="differenziata">Differenziata</option>
                                    <option value="obiettivi minimi">Obiettivi Minimi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="monte_ore">Numero Ore Settimanali <span class="required">*</span></label>
                                <div class="input-with-suffix">
                                    <input type="number" id="monte_ore" name="monte_ore" min="1" max="40" placeholder="Es. 9" required>
                                    <span class="suffix">ore / sett.</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="note">Note sul supporto educativo</label>
                            <textarea id="note" name="note" rows="4" placeholder="Inserisci eventuali patologie (se necessario) o indicazioni per i tutor..." style="width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem; outline: none; background-color: white; resize: vertical;"></textarea>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <div class="section-header">
                            <span class="section-icon">🔗</span>
                            <h2>Assegnazioni</h2>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tutor_id">Tutor (solo uno)</label>
                                <select id="tutor_id" name="tutor_id">
                                    <option value="">-- Nessuno --</option>
                                    <?php foreach ($tutors as $t): ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['last_name'] . ' ' . $t['first_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="teacher_ids">Insegnanti (più di uno)</label>
                                <select id="teacher_ids" name="teacher_ids[]" multiple size="4">
                                    <?php foreach ($teachers as $te): ?>
                                        <option value="<?php echo $te['id']; ?>"><?php echo htmlspecialchars($te['last_name'] . ' ' . $te['first_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="studenti.php" class="btn-cancel">Annulla</a>
                        <button type="submit" class="btn-primary">Salva Studente</button>
                    </div>

                </form>
            </main>
        </div>
    </div>
</body>
</html>

