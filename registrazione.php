<?php
session_start();

$conn = new mysqli("localhost", "rizzo", "03022005", "sostegno");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$errore = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errore = "Questa email è già registrata nel portale.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
            $stmt_ins = $conn->prepare($sql);
            if ($stmt_ins) {
                $stmt_ins->bind_param("ss", $email, $hashed_password);
                if ($stmt_ins->execute()) {
                    header("Location: login.php?registrato=1");
                    exit;
                } else {
                    $errore = "Si è verificato un errore durante il salvataggio.";
                }
                $stmt_ins->close();
            }
        }
        $stmt->close();
    } else {
        $errore = "Tutti i campi sono obbligatori.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Portale Sostegno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="registration-body">

    <div class="header-section">
        <div class="icon-lock-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
        <h1>Portale Sostegno</h1>
        <p>Gestione riservata dati studenti</p>
    </div>

    <div class="register-card">
        
        <?php if (!empty($errore)): ?>
            <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2; margin-bottom: 20px;">
                <div class="alert-icon">⚠️</div>
                <div class="alert-content">
                    <strong style="color: #991b1b;">Errore di registrazione</strong>
                    <p style="color: #991b1b;"><?php echo $errore; ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-box">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div class="alert-content">
                    <strong>Accesso Riservato</strong>
                    <p>Registrati inserendo le tue credenziali istituzionali. Il profilo verrà verificato dagli amministratori.</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group-reg">
                <label for="email">Email Istituzionale</label>
                <input type="email" id="email" name="email" placeholder="nome.cognome@scuola.edu.it" required>
            </div>

            <div class="form-group-reg">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit-reg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Crea Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #2563eb; text-decoration: none; font-size: 0.95rem;">Hai già un account? Accedi</a>
        </div>
    </div>

</body>
</html>