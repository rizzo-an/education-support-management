<?php
$host = "localhost";
$username = "root"; 
$password = "";     
$database = "sostegno";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['nome'] ?? '';
    $last_name = $_POST['cognome'] ?? '';
    
    $birth_date = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : '2000-01-01'; 
    
    $class = $_POST['classe'] ?? '';
    $city = $_POST['comune'] ?? $_POST['city'] ?? ''; 
    $hours = !empty($_POST['monte_ore']) ? intval($_POST['monte_ore']) : 0;
    $study_type = $_POST['programmazione'] ?? $_POST['study_type'] ?? 'Nessuna';

    $sql = "INSERT INTO students (first_name, last_name, birth_date, class, city, study_type, hours) 
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