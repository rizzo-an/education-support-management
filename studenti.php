<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$cities = [];
$cityResult = $conn->query("SELECT DISTINCT city FROM students WHERE city IS NOT NULL AND TRIM(city) <> '' ORDER BY city ASC");
if ($cityResult) {
    while ($row = $cityResult->fetch_assoc()) {
        $cities[] = $row['city'];
    }
}

$classes = [];
$classResult = $conn->query("SELECT DISTINCT class FROM students WHERE class IS NOT NULL AND TRIM(class) <> '' ORDER BY class ASC");
if ($classResult) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row['class'];
    }
}

$sostegni = [];
$sostegnoResult = $conn->query("SELECT DISTINCT CASE WHEN hours > 0 THEN 'Assegnato' ELSE 'Non assegnato' END AS label FROM students WHERE hours IS NOT NULL ORDER BY label ASC");
if ($sostegnoResult) {
    while ($row = $sostegnoResult->fetch_assoc()) {
        $sostegni[] = $row['label'];
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Sostegno - Studenti</title>
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
                <a href="#" class="nav-item active">
                    <span class="icon">🪟</span> Studenti
                </a>
                <a href="tutor.php" class="nav-item">
                    <span class="icon">👤</span> Tutor
                </a>
                <a href="insegnanti.php" class="nav-item">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="cookie-btn">Manage cookies or opt out</button>
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
                        <h1>Studenti con Disabilità</h1>
                        <p>Gestisci anagrafiche, ore di sostegno e tutor assegnati.</p>
                    </div>
                    <a href="nuovo_studente.php">
                        <button class="btn-primary">+ Nuovo Studente</button>
                    </a>
                </div>

                <div class="filter-panel">
                    <div class="filter-group filter-search">
                        <label>RICERCA</label>
                        <div class="search-input-wrapper">
                            <span class="search-icon">🔍</span>
                            <input type="text" id="cercaStudente" placeholder="Cerca per nome o cognome...">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>COMUNE</label>
                        <select id="filtroComune">
                            <option value="">Tutti</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>CLASSE</label>
                        <select id="filtroClasse">
                            <option value="">Tutte</option>
                            <?php foreach ($classes as $classe): ?>
                                <option value="<?php echo htmlspecialchars($classe); ?>"><?php echo htmlspecialchars($classe); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>SOSTEGNO</label>
                        <select id="filtroSostegno">
                            <option value="">Tutti</option>
                            <?php foreach ($sostegni as $label): ?>
                                <option value="<?php echo htmlspecialchars($label); ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>STUDENTE</th>
                                <th>CLASSE / COMUNE</th>
                                <th>SOSTEGNO</th>
                                <th>PROG.</th>
                                <th>AZIONI</th>
                            </tr>
                        </thead>
                        <tbody id="studentiTableBody">
                            <?php
                            $sql = "SELECT * FROM students ORDER BY last_name ASC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $id = $row['id'];
                                    $nomeCompleto = htmlspecialchars($row['last_name'] . " " . $row['first_name']);
                                    $classeComune = htmlspecialchars($row['class'] . " / " . $row['city']);
                                    $oreSostegno = htmlspecialchars($row['hours'] . " ore/sett");
                                    $programmazione = htmlspecialchars($row['study_type']);
                                    ?>
                                    <tr data-city="<?php echo htmlspecialchars($row['city'] ?? ''); ?>" data-class="<?php echo htmlspecialchars($row['class'] ?? ''); ?>" data-sostegno="<?php echo $row['hours'] > 0 ? 'Assegnato' : 'Non assegnato'; ?>">
                                        <td><strong><?php echo $nomeCompleto; ?></strong></td>
                                        <td><?php echo $classeComune; ?></td>
                                        <td><span class="badge"><?php echo $oreSostegno; ?></span></td>
                                        <td><?php echo $programmazione; ?></td>
                                        <td>
                                            <a href="modifica_studente.php?id=<?php echo $id; ?>" class="btn-outline" style="padding: 4px 10px; font-size: 0.85rem; text-decoration: none; display: inline-block; margin-right: 5px;">Modifica</a>
                                            <a href="elimina_studente.php?id=<?php echo $id; ?>" class="btn-outline" style="padding: 4px 10px; font-size: 0.85rem; text-decoration: none; display: inline-block; color: #dc2626; border-color: #fca5a5;" onclick="return confirm('Sei sicuro di voler eliminare lo studente <?php echo $nomeCompleto; ?>?');">Elimina</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align: center; padding: 20px; color: #64748b;'>Nessuno studente presente nel database.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <?php $conn->close(); ?>
    <script src="studenti.js"></script>
</body>
</html>