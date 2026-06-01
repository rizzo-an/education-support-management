<?php
session_start();

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    $conn->begin_transaction();
    try {
        // Elimina relazioni con tutor
        $stmt1 = $conn->prepare("DELETE FROM tutors_students WHERE student_id = ?");
        if ($stmt1) {
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $stmt1->close();
        }

        // Elimina relazioni con insegnanti
        $stmt2 = $conn->prepare("DELETE FROM teachers_students WHERE student_id = ?");
        if ($stmt2) {
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();
        }

        // Elimina studente
        $stmt3 = $conn->prepare("DELETE FROM students WHERE id = ?");
        if ($stmt3) {
            $stmt3->bind_param("i", $id);
            $stmt3->execute();
            $stmt3->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Errore durante l'eliminazione: " . $e->getMessage());
    }
}

$conn->close();

header("Location: studenti.php");
exit;
?>