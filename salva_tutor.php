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
    $telephone_number = $_POST['telefono'] ?? '';
    $cooperative_id = $_POST['cooperativa'] ?? '';
  

    $sql = "INSERT INTO tutors (first_name, last_name, telephone_number, cooperative_id) 
        VALUES (?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        $stmt->bind_param("ssss", $first_name, $last_name, $telephone_number, $cooperative_id);
       
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