<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: studenti.php"); 
    exit;
}

$errore = "";
$msg_successo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        if ($username === 'utente' && $password === 'utente123!') {
            $_SESSION['user_id'] = 1;
            $_SESSION['email'] = 'utente@localhost';

            header("Location: studenti.php");
            exit;
        } else {
            $errore = "Username o password errata. Riprova.";
        }
    } else {
        $errore = "Compila tutti i campi richiesti.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portale Sostegno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

<div class="login-wrapper">
    <div class="login-header">
        <div class="icon-lock-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
        <h1>Portale Sostegno</h1>
        <p>Gestione riservata dati studenti</p>
    </div>
        

        <div class="login-card">
            
            <?php if (!empty($errore)): ?>
                <div class="alert-box" style="border-left: 4px solid #dc2626; background: #fef2f2;">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-content">
                        <strong style="color: #991b1b;">Errore di accesso</strong>
                        <p style="color: #991b1b;"><?php echo $errore; ?></p>
                    </div>
                </div>
            <?php elseif (!empty($msg_successo)): ?>
                <div class="alert-box" style="border-left: 4px solid #16a34a; background: #f0fdf4;">
                    <div class="alert-icon">✅</div>
                    <div class="alert-content">
                        <strong style="color: #166534;">Operazione completata</strong>
                        <p style="color: #166534;"><?php echo $msg_successo; ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert-box">
                    <div class="alert-icon">🛡️</div>
                    <div class="alert-content">
                        <strong>Accesso Riservato</strong>
                        <p>Sistema accessibile esclusivamente a docenti autorizzati.<br>Tutti gli accessi sono monitorati.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Inserisci username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-login">
                    <span class="btn-icon">➔</span> Accedi al portale
                </button>
            </form>

        </div>
    </div>

</body>
</html>