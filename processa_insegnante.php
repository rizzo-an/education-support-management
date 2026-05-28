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
    $email = $_POST['email'] ?? '';
 

    $sql = "INSERT INTO teachers ( first_name, last_name, email) 
            VALUES (?, ?, ?)";
            
    $stmt = $conn->prepare($sql);

    if ($stmt) {
    
        $stmt->bind_param("sss", $first_name, $last_name, $email);
       
        if ($stmt->execute()) {
     
            $stmt->close();
            $conn->close();
            header("Location: insegnanti.php");
            exit();
        } else {
            echo "Errore durante il salvataggio: " . $stmt->error;
        }
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
} else {
    
    header("Location: nuovoinsegnante.php");
    exit();
}

$conn->close();
?>