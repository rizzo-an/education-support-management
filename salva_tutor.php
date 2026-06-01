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
    
    
    $first_name = trim($_POST['nome'] ?? '');
    $last_name = trim($_POST['cognome'] ?? '');
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $birth_date = !empty($_POST['data_nascita']) ? $_POST['data_nascita'] : null;
    $telephone_number = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cooperative_id = !empty($_POST['cooperativa']) ? intval($_POST['cooperativa']) : null;
    $monte_ore = !empty($_POST['monte_ore']) ? intval($_POST['monte_ore']) : null;
    $note = trim($_POST['note'] ?? '');

    $sql = "INSERT INTO tutors (first_name, last_name, codice_fiscale, birth_date, telephone_number, email, cooperative_id, monte_ore, note) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        $stmt->bind_param("ssssssissi", $first_name, $last_name, $codice_fiscale, $birth_date, $telephone_number, $email, $cooperative_id, $monte_ore, $note);
       
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
         
            header("Location: tutor.php");
            exit();
        } else {
            echo "Errore durante il salvataggio: " . $stmt->error;
        }
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
} else {
    header("Location: nuovo_tutor.php");
    exit();
}

$conn->close();
?>