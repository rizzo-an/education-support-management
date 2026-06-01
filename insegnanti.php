<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Sostegno - Insegnanti</title>
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
                <a href="#" class="nav-item active">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
                <a href="cooperative.php" class="nav-item">
                    <span class="icon">🏢</span> Cooperative
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="cookie-btn">Gestisci Cookie</button>
            </div>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Dashboard</div>
                
                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div style="display: flex; gap: 10px;">
                            <a href="login.php" class="btn-outline" style="padding: 6px 12px; text-decoration: none; font-size: 0.9rem; border-radius: 6px;">Accedi</a>
                            <a href="registrazione.php" class="btn-primary" style="padding: 6px 12px; text-decoration: none; font-size: 0.9rem; border-radius: 6px; color: white; display: inline-block;">Registrati</a>
                        </div>
                    <?php endif; ?>

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
                </div>
            </header>

            <main class="content">
                <div class="page-header">
                    <div>
                        <h1>Insegnanti di Sostegno</h1>
                        <p>Elenco dei docenti di ruolo ed organico di fatto assegnati all'istituto.</p>
                    </div>
                    <a href="nuovoinsegnante.php">
                        <button class="btn-primary">+ Nuovo Insegnante</button>
                    </a>
                </div>

                <div class="cards-grid">
                    <?php
                    $conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

                    if ($conn->connect_error) {
                        die("Connessione fallita: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM teachers ORDER BY last_name ASC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            $nomeCompleto = htmlspecialchars($row['last_name'] . " " . $row['first_name']);
                            $email = htmlspecialchars($row['email']);
                            
                            echo '
                            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 200px;">
                                <div>
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="badge-green">COMPLETA</span>
                                    </div>
                                    
                                    <h3>' . $nomeCompleto . '</h3>
                                    <p class="coop">Docente MIUR</p>
                                    
                                    <a href="mailto:' . $email . '" class="email-link">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        ' . $email . '
                                    </a>
                                </div>
                                
                                <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                                    <a href="modifica_insegnante.php?id=' . $id . '" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block;">Modifica</a>
                                    
                                    <a href="elimina_insegnante.php?id=' . $id . '" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block; color: #dc2626; border-color: #fca5a5;" onclick="return confirm(\'Sei sicuro di voler eliminare l\\\'insegnante ' . $nomeCompleto . '?\');">Elimina</a>
                                </div>
                            </div>';
                        }
                    } else {
                        echo "<p style='grid-column: span 3; text-align: center; color: #64748b; padding: 20px;'>Nessun insegnante presente nel database.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>