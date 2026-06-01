<?php
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");
    
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    
    $conn->begin_transaction();
    try {
        // Elimina relazioni con studenti
        $stmt1 = $conn->prepare("DELETE FROM tutors_students WHERE tutor_id = ?");
        if ($stmt1) {
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $stmt1->close();
        }

        // Elimina tutor
        $stmt2 = $conn->prepare("DELETE FROM tutors WHERE id = ?");
        if ($stmt2) {
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Errore durante l'eliminazione: " . $e->getMessage());
    }
    
    $conn->close();
}

header("Location: tutor.php");
exit;
?>