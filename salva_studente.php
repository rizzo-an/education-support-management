<?php
$host = "localhost";
$username = "rizzo"; 
$password = "03022005";     
$database = "sostegno";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

function normalizeStudyType($value) {
    $map = [
        'PEI Differenziato' => 'differenziata',
        'PEI Semplificato' => 'obiettivi minimi',
        'Personalizzata' => 'differenziata',
        'Personalizzata (BES/DSA)' => 'differenziata',
        'Differenziata' => 'differenziata',
        'Obiettivi Minimi' => 'obiettivi minimi',
        'differenziata' => 'differenziata',
        'obiettivi minimi' => 'obiettivi minimi',
    ];

    return $map[$value] ?? 'differenziata';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['nome'] ?? '';
    $last_name = $_POST['cognome'] ?? '';
    
    $birth_date = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : '2000-01-01'; 
    
    $class = $_POST['classe'] ?? '';
    $city = $_POST['comune'] ?? $_POST['city'] ?? ''; 
    $hours = !empty($_POST['monte_ore']) ? intval($_POST['monte_ore']) : 0;
    $note = trim($_POST['note'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $study_type = normalizeStudyType($_POST['tipo_programmazione'] ?? $_POST['programmazione'] ?? $_POST['study_type'] ?? 'differenziata');

    $sql = "INSERT INTO students (`first_name`, `last_name`, `birth_date`, `class`, `city`, `study_type`, `hours`, `note`, `email`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssiss", $first_name, $last_name, $birth_date, $class, $city, $study_type, $hours, $note, $email);
       
        if ($stmt->execute()) {
            // ottieni id studente inserito
            $student_id = $conn->insert_id;

            // inizia transazione per inserire relazioni
            $conn->begin_transaction();
            try {
                // tutor (solo uno)
                if (!empty($_POST['tutor_id'])) {
                    $tutor_id = intval($_POST['tutor_id']);
                    $insTutor = $conn->prepare("INSERT INTO tutors_students (student_id, tutor_id) VALUES (?, ?)");
                    if ($insTutor) {
                        $insTutor->bind_param("ii", $student_id, $tutor_id);
                        $insTutor->execute();
                        $insTutor->close();
                    }
                }

                // insegnanti (più di uno)
                if (!empty($_POST['teacher_ids']) && is_array($_POST['teacher_ids'])) {
                    $insTeacher = $conn->prepare("INSERT INTO teachers_students (student_id, teacher_id) VALUES (?, ?)");
                    if ($insTeacher) {
                        foreach ($_POST['teacher_ids'] as $tid) {
                            $t = intval($tid);
                            $insTeacher->bind_param("ii", $student_id, $t);
                            $insTeacher->execute();
                        }
                        $insTeacher->close();
                    }
                }

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
            }

            $stmt->close();
            $conn->close();
            header("Location: studenti.php");
            exit();
        } else {
            echo "Errore durante il salvataggio: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
}
$conn->close();
?>