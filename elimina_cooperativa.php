<?php
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    $canDelete = true;
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS total FROM tutors WHERE cooperative_id = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult && $row = $checkResult->fetch_assoc()) {
            if ($row['total'] > 0) {
                $canDelete = false;
            }
        }
        $checkStmt->close();
    }

    if ($canDelete) {
        $stmt = $conn->prepare("DELETE FROM cooperatives WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
        header("Location: cooperative.php");
        exit;
    }

    $conn->close();
    header("Location: cooperative.php?error=1");
    exit;
}

header("Location: cooperative.php");
exit;
?>
