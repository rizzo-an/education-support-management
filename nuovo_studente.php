<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Studente - Gestione Sostegno</title>
    <link rel="stylesheet" href="style.css">
    <script src="../js/general.js" defer></script>
</head>
<?php

$iniziali_utente = "AD"; 
$nome_cognome_utente = "Amministratore";
$ruolo_utente = "Admin";
?>
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
                <button class="hamburger-btn" id="mobileMenuBtn">☰</button>
                <div class="topbar-title">Dashboard</div>
                <div class="user-profile-wrapper">          
                    <div class="user-profile-group">
                        <div class="avatar"><?php echo $iniziali_utente; ?></div>
                        <div class="user-info">
                            <strong><?php echo $nome_cognome_utente; ?></strong>
                            <span><?php echo $ruolo_utente; ?></span>
                        </div>
                    </div>
                    <div class="profile-logout-divider"></div>
                    <a href="logout.php" class="logout-icon-link" title="Esci"><span class="icon-logout">🚪</span></a>
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
                    
                    <div class="form-section-card">
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
                                    <option value="PEI Differenziato">PEI Differenziato</option>
                                    <option value="PEI Semplificato">PEI Semplificato (Obiettivi Minimi)</option>
                                    <option value="Personalizzata">Personalizzata (BES/DSA)</option>
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

