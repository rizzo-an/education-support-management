<?php
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
    
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
}

header("Location: insegnanti.php?eliminato=1");
exit;
?>