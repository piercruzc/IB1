<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Complete todos los campos.';
    }
}

// If already logged in, redirect
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IBM FINTECH Blog</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../img/logo.png" alt="IBM FINTECH" class="login-logo">
                <h1>Panel Administrativo</h1>
                <p>Blog IBM FINTECH</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required autocomplete="username"
                           value="<?= htmlspecialchars($username ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
