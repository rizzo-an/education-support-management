<?php
session_start();

function normalizeStudyType($value) {
    $map = [
        'Personalizzata' => 'differenziata',
        'Differenziata' => 'differenziata',
        'Minima' => 'obiettivi minimi',
        'Obiettivi Minimi' => 'obiettivi minimi',
        'PEI Differenziato' => 'differenziata',
        'PEI Semplificato' => 'obiettivi minimi',
        'Personalizzata (BES/DSA)' => 'differenziata',
        'Nessuna' => 'differenziata',
        'differenziata' => 'differenziata',
        'obiettivi minimi' => 'obiettivi minimi',
    ];

    return $map[$value] ?? 'differenziata';
}

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $studente = $result->fetch_assoc();
    } else {
        die("Studente non trovato.");
    }
    $stmt->close();
} else {
    die("ID studente non valido.");
}

// carica tutor e insegnanti per le selezioni
$tutors = [];
$teachers = [];
$selected_tutor = null;
$selected_teachers = [];
$resT = $conn->query("SELECT id, first_name, last_name FROM tutors ORDER BY last_name ASC");
if ($resT) { while ($r = $resT->fetch_assoc()) $tutors[] = $r; }
$resTe = $conn->query("SELECT id, first_name, last_name FROM teachers ORDER BY last_name ASC");
if ($resTe) { while ($r = $resTe->fetch_assoc()) $teachers[] = $r; }

// carica relazioni esistenti
$stmtRel = $conn->prepare("SELECT tutor_id FROM tutors_students WHERE student_id = ? LIMIT 1");
if ($stmtRel) {
    $stmtRel->bind_param("i", $id);
    $stmtRel->execute();
    $res = $stmtRel->get_result();
    if ($res && $row = $res->fetch_assoc()) $selected_tutor = $row['tutor_id'];
    $stmtRel->close();
}
$stmtRel2 = $conn->prepare("SELECT teacher_id FROM teachers_students WHERE student_id = ?");
if ($stmtRel2) {
    $stmtRel2->bind_param("i", $id);
    $stmtRel2->execute();
    $res2 = $stmtRel2->get_result();
    if ($res2) { while ($r = $res2->fetch_assoc()) $selected_teachers[] = $r['teacher_id']; }
    $stmtRel2->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['nome'] ?? '';
    $last_name = $_POST['cognome'] ?? '';
    $birth_date = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : '2000-01-01';
    $class = $_POST['classe'] ?? '';
    $city = $_POST['comune'] ?? '';
    $hours = !empty($_POST['monte_ore']) ? intval($_POST['monte_ore']) : 0;
    $note = $_POST['note'] ?? '';
    $study_type = normalizeStudyType($_POST['programmazione'] ?? $_POST['tipo_programmazione'] ?? $_POST['study_type'] ?? 'differenziata');

    $sql_update = "UPDATE students SET first_name = ?, last_name = ?, birth_date = ?, class = ?, city = ?, study_type = ?, hours = ?, note = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update) {
        $stmt_update->bind_param("ssssssisi", $first_name, $last_name, $birth_date, $class, $city, $study_type, $hours, $note, $id);
        if ($stmt_update->execute()) {
            $conn->begin_transaction();
            try {
                // elimina relazioni esistenti
                $del1 = $conn->prepare("DELETE FROM tutors_students WHERE student_id = ?");
                if ($del1) { $del1->bind_param("i", $id); $del1->execute(); $del1->close(); }

                $del2 = $conn->prepare("DELETE FROM teachers_students WHERE student_id = ?");
                if ($del2) { $del2->bind_param("i", $id); $del2->execute(); $del2->close(); }

                // inserisci nuovo tutor (se presente)
                if (!empty($_POST['tutor_id'])) {
                    $tutor_id = intval($_POST['tutor_id']);
                    $insT = $conn->prepare("INSERT INTO tutors_students (student_id, tutor_id) VALUES (?, ?)");
                    if ($insT) { $insT->bind_param("ii", $id, $tutor_id); $insT->execute(); $insT->close(); }
                }

                // inserisci insegnanti
                if (!empty($_POST['teacher_ids']) && is_array($_POST['teacher_ids'])) {
                    $insTe = $conn->prepare("INSERT INTO teachers_students (student_id, teacher_id) VALUES (?, ?)");
                    if ($insTe) {
                        foreach ($_POST['teacher_ids'] as $tid) {
                            $t = intval($tid);
                            $insTe->bind_param("ii", $id, $t);
                            $insTe->execute();
                        }
                        $insTe->close();
                    }
                }

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
            }
            header("Location: studenti.php");
            exit;
        } else {
            echo "<script>alert('Errore durante l\'aggiornamento del database.');</script>";
        }
        $stmt_update->close();
    } else {
        echo "<script>alert('Errore nella preparazione della query di aggiornamento.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Studente - Gestione Sostegno</title>
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
                <a href="studenti.php" class="nav-item active">
                    <span class="icon">🪟</span> Studenti
                </a>
                <a href="tutor.php" class="nav-item">
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
                <div class="topbar-title">Modifica Studente</div>
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
                        <h1>Modifica Anagrafica Studente</h1>
                        <p>Aggiorna i dati di <?php echo htmlspecialchars($studente['first_name'] . " " . $studente['last_name']); ?></p>
                    </div>
                </div>

                <form action="" method="POST" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="nome">Nome <span style="color: red;">*</span></label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($studente['first_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label for="cognome">Cognome <span style="color: red;">*</span></label>
                            <input type="text" id="cognome" name="cognome" value="<?php echo htmlspecialchars($studente['last_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label for="data_nascita">Data di Nascita</label>
                            <input type="date" id="data_nascita" name="data_nascita" value="<?php echo htmlspecialchars($studente['birth_date']); ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label for="classe">Classe <span style="color: red;">*</span></label>
                            <input type="text" id="classe" name="classe" value="<?php echo htmlspecialchars($studente['class']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label for="comune">Comune / Città</label>
                            <input type="text" id="comune" name="comune" value="<?php echo htmlspecialchars($studente['city']); ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label for="monte_ore">Ore Settimanali <span style="color: red;">*</span></label>
                            <input type="number" id="monte_ore" name="monte_ore" min="1" max="40" value="<?php echo htmlspecialchars($studente['hours']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label for="programmazione">Tipo Programmazione</label>
                            <select id="programmazione" name="programmazione" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;">
                                <option value="differenziata" <?php if($studente['study_type'] == 'differenziata') echo 'selected'; ?>>Differenziata</option>
                                <option value="obiettivi minimi" <?php if($studente['study_type'] == 'obiettivi minimi') echo 'selected'; ?>>Obiettivi Minimi</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px; grid-column: span 2;">
                        <label for="note">Note sul supporto educativo</label>
                        <textarea id="note" name="note" rows="4" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; resize: vertical;"><?php echo htmlspecialchars($studente['note'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-section" style="margin-top:20px;">
                        <h3>Assegnazioni</h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:10px;">
                            <div class="form-group">
                                <label for="tutor_id">Tutor (solo uno)</label>
                                <select id="tutor_id" name="tutor_id" style="width:100%;padding:10px;">
                                    <option value="">-- Nessuno --</option>
                                    <?php foreach($tutors as $t): ?>
                                        <option value="<?php echo $t['id']; ?>" <?php if($selected_tutor == $t['id']) echo 'selected'; ?>><?php echo htmlspecialchars($t['last_name'].' '.$t['first_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="teacher_ids">Insegnanti (più di uno)</label>
                                <select id="teacher_ids" name="teacher_ids[]" multiple size="4" style="width:100%;padding:10px;">
                                    <?php foreach($teachers as $te): ?>
                                        <option value="<?php echo $te['id']; ?>" <?php if(in_array($te['id'],$selected_teachers)) echo 'selected'; ?>><?php echo htmlspecialchars($te['last_name'].' '.$te['first_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
                        <a href="studenti.php" class="btn-outline" style="text-decoration: none; padding: 10px 20px; line-height: 20px;">Annulla</a>
                        <button type="submit" class="btn-primary" style="padding: 10px 20px; cursor: pointer;">Salva Modifiche</button>
                    </div>

                </form>
            </main>
        </div>
    </div>
</body>
</html>