<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Sostegno - Tutor</title>
    <link rel="stylesheet" href="style.css">
    <script src="../js/general.js" defer></script>
    <script src="../js/tutor.js" defer></script>
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
                            <?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : "U"; ?>
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
                        <h1>Educatori / Tutor</h1>
                        <p>Visualizza i tutor educativi e le cooperative di appartenenza.</p>
                    </div>
                    <a href="nuovo_tutor.php">
                        <button class="btn-primary">+ Nuovo Tutor</button>
                    </a>
                </div>

                <div class="cards-grid" id="tutorGrid">
                <?php
                $conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

                if ($conn->connect_error) {
                    die("Connessione fallita: " . $conn->connect_error);
                }

                $sql = "SELECT tutors.*, cooperatives.name AS nome_cooperativa 
                        FROM tutors 
                        LEFT JOIN cooperatives ON tutors.cooperative_id = cooperatives.id";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        
                        $nomeCompleto = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
                        $cooperativa = htmlspecialchars($row['nome_cooperativa'] ?? 'Nessuna Cooperativa');
                        $telefono = htmlspecialchars($row['telephone_number']);
                        $ruolo = "TUTOR"; 
                        ?>
                        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 200px;">
                            <div>
                                <div class="card-header">
                                    <div class="card-icon">👤</div>
                                    <span class="badge"><?php echo $ruolo; ?></span>
                                </div>
                                <h3><?php echo $nomeCompleto; ?></h3>
                                <p class="coop"><?php echo $cooperativa; ?></p>
                                <div class="phone"><span>📞</span> <?php echo $telefono; ?></div>
                            </div>
                            
                            <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                                <a href="modifica_tutor.php?id=<?php echo $id; ?>" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block;">Modifica</a>
                                
                                <a href="elimina_tutor.php?id=<?php echo $id; ?>" class="btn-outline" style="flex: 1; text-align: center; padding: 6px 10px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; display: inline-block; color: #dc2626; border-color: #fca5a5;" onclick="return confirm('Sei sicuro di voler eliminare il tutor <?php echo $nomeCompleto; ?>?');">Elimina</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='grid-column: span 3; text-align: center; color: #64748b; padding: 20px;'>Nessun tutor presente nel database.</p>";
                }
                $conn->close();
                ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>