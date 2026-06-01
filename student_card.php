<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id <= 0) {
    die('ID studente non valido.');
}

$conn = new mysqli('localhost', 'rizzo', '03022005', 'sostegno');
if ($conn->connect_error) {
    die('Connessione fallita: ' . $conn->connect_error);
}

$sql = "SELECT s.*, CONCAT(t.last_name, ' ', t.first_name) AS tutor_name, t.id AS tutor_id
        FROM students s
        LEFT JOIN tutors_students ts ON s.id = ts.student_id
        LEFT JOIN tutors t ON ts.tutor_id = t.id
        WHERE s.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die('Studente non trovato.');
}

$student = $result->fetch_assoc();
$stmt->close();

$teachers = [];
$stmt2 = $conn->prepare("SELECT te.first_name, te.last_name
                        FROM teachers_students ts
                        JOIN teachers te ON ts.teacher_id = te.id
                        WHERE ts.student_id = ?");
$stmt2->bind_param('i', $student_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        $teachers[] = $row;
    }
}
$stmt2->close();
$conn->close();

$fullName = htmlspecialchars($student['first_name'] . ' ' . $student['last_name']);
$birthDate = htmlspecialchars($student['birth_date'] ?? '');
$class = htmlspecialchars($student['class'] ?? '');
$city = htmlspecialchars($student['city'] ?? '');
$studyType = htmlspecialchars($student['study_type'] ?? '');
$hours = htmlspecialchars($student['hours'] ?? 0);
$tutorName = htmlspecialchars($student['tutor_name'] ?? 'Non assegnato');
$teacherNames = array_map(function ($item) {
    return htmlspecialchars($item['last_name'] . ' ' . $item['first_name']);
}, $teachers);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheda Studente - <?php echo $fullName; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .student-card-container { max-width: 1100px; margin: 0 auto; }
        .student-card { display: grid; gap: 24px; grid-template-columns: 1.2fr 0.8fr; }
        .student-card-main, .student-card-side { background: white; border: 1px solid var(--border-color); border-radius: 18px; padding: 28px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); }
        .student-card-main h2, .student-card-side h2 { margin-bottom: 18px; font-size: 1.3rem; }
        .student-info-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 18px; }
        .info-block { background: var(--bg-color); border-radius: 14px; padding: 18px; }
        .info-block strong { display: block; margin-bottom: 8px; color: var(--text-main); }
        .info-block span { color: var(--text-muted); font-size: 0.95rem; }
        .relation-list { list-style: none; padding: 0; margin: 0; }
        .relation-list li { padding: 12px 0; border-bottom: 1px solid #f0f0f7; }
        .relation-list li:last-child { border-bottom: none; }
        .relation-list strong { display: block; color: var(--text-main); }
        .back-group { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
        .student-card-badge { display: inline-flex; align-items: center; justify-content: center; background: var(--primary-light); color: var(--primary-color); border-radius: 999px; padding: 6px 14px; font-weight: 700; letter-spacing: 0.4px; }
        @media (max-width: 900px) {
            .student-card { grid-template-columns: 1fr; }
            .info-block { padding: 16px; }
        }
    </style>
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
                <a href="insegnanti.php" class="nav-item">
                    <span class="icon">📖</span> Insegnanti di Sostegno
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-title">Scheda Studente</div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="user-profile">
                        <div class="avatar"><?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'U'; ?></div>
                        <div class="user-info">
                            <strong><?php echo isset($_SESSION['email']) ? htmlspecialchars(explode('@', $_SESSION['email'])[0]) : 'Ospite'; ?></strong>
                            <span>Docente Autorizzato</span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="logout.php" style="font-size: 0.8rem; color: #dc2626; text-decoration: none; display: block; margin-top: 2px;">Disconnetti</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content student-card-container">
                <div class="back-group">
                    <div>
                        <h1><?php echo $fullName; ?></h1>
                        <p style="color: var(--text-muted); margin-top: 8px;">Scheda dettagliata dello studente con relazioni tutor e insegnanti.</p>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap: wrap; align-items:center;">
                        <span class="student-card-badge"><?php echo $student['study_type'] ?? 'Non definito'; ?></span>
                        <a href="studenti.php" class="btn-outline" style="text-decoration:none;">← Torna all'elenco</a>
                    </div>
                </div>

                <div class="student-card">
                    <section class="student-card-main">
                        <h2>Informazioni anagrafiche</h2>
                        <div class="student-info-grid">
                            <div class="info-block">
                                <strong>Nome completo</strong>
                                <span><?php echo $fullName; ?></span>
                            </div>
                            <div class="info-block">
                                <strong>Data di nascita</strong>
                                <span><?php echo $birthDate ?: 'Non specificata'; ?></span>
                            </div>
                            <div class="info-block">
                                <strong>Classe</strong>
                                <span><?php echo $class ?: 'Non specificata'; ?></span>
                            </div>
                            <div class="info-block">
                                <strong>Comune</strong>
                                <span><?php echo $city ?: 'Non specificato'; ?></span>
                            </div>
                        </div>

                        <div class="student-info-grid" style="margin-top: 20px;">
                            <div class="info-block">
                                <strong>Ore di sostegno settimanali</strong>
                                <span><?php echo $hours ? $hours . ' ore' : 'Non assegnate'; ?></span>
                            </div>
                            <div class="info-block">
                                <strong>Tipologia di programmazione</strong>
                                <span><?php echo $studyType ?: 'Non definita'; ?></span>
                            </div>
                        </div>
                    </section>

                    <aside class="student-card-side">
                        <h2>Relazioni attive</h2>
                        <div class="info-block" style="background:white; box-shadow: inset 0 0 0 1px rgba(226,226,236,0.5);">
                            <strong>Tutor assegnato</strong>
                            <span><?php echo $tutorName ?: 'Non assegnato'; ?></span>
                        </div>
                        <div class="info-block" style="background:white; box-shadow: inset 0 0 0 1px rgba(226,226,236,0.5); margin-top: 16px;">
                            <strong>Insegnanti assegnati</strong>
                            <?php if (count($teacherNames) > 0): ?>
                                <ul class="relation-list">
                                    <?php foreach ($teacherNames as $name): ?>
                                        <li><?php echo $name; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span>Ancora nessun insegnante assegnato</span>
                            <?php endif; ?>
                        </div>
                    </aside>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
