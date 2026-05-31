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
    $study_type = normalizeStudyType($_POST['tipo_programmazione'] ?? $_POST['programmazione'] ?? $_POST['study_type'] ?? 'differenziata');

    $sql = "INSERT INTO students (`first_name`, `last_name`, `birth_date`, `class`, `city`, `study_type`, `hours`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssi", $first_name, $last_name, $birth_date, $class, $city, $study_type, $hours);
       
        if ($stmt->execute()) {
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