<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();
$errors = [];
$success = '';

$userId = $_SESSION['admin_id'] ?? null;
if (!$userId) {
    // Fallback: get by username
    $stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$_SESSION['admin_user']]);
    $row = $stmt->fetch();
    $userId = $row['id'] ?? null;
}

// Handle username change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'username') {
    $newUser = trim($_POST['new_username'] ?? '');
    if (!$newUser || strlen($newUser) < 3) {
        $errors[] = 'El usuario debe tener al menos 3 caracteres.';
    } else {
        $check = $db->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
        $check->execute([$newUser, $userId]);
        if ($check->fetch()) {
            $errors[] = 'Ese nombre de usuario ya existe.';
        } else {
            $db->prepare("UPDATE admin_users SET username = ? WHERE id = ?")->execute([$newUser, $userId]);
            $_SESSION['admin_user'] = $newUser;
            $success = 'Usuario actualizado correctamente.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'password') {
    $current = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Verify current password
    $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password_hash'])) {
        $errors[] = 'La contraseña actual es incorrecta.';
    } elseif (strlen($newPass) < 8) {
        $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($newPass !== $confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);
        $success = 'Contraseña actualizada correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Admin Blog</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="../img/logo.png" alt="IBM FINTECH" class="sidebar-logo">
                <span>Blog Admin</span>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                    Dashboard
                </a>
                <a href="create.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nuevo Post
                </a>
                <a href="categories.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                    </svg>
                    Categorías
                </a>
                <a href="settings.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Configuración
                </a>
                <a href="account.php" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    Mi Cuenta
                </a>
            </nav>
            <div class="sidebar-footer">
                <span class="admin-user"><?= htmlspecialchars($_SESSION['admin_user']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Mi Cuenta</h1>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="categories-layout">
                <!-- Change Username -->
                <div class="form-card" style="position:sticky; top:30px; align-self:start;">
                    <h3>Cambiar Usuario</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="username">
                        <div class="form-group">
                            <label for="new_username">Nuevo usuario</label>
                            <input type="text" id="new_username" name="new_username" required
                                   value="<?= htmlspecialchars($_SESSION['admin_user']) ?>"
                                   minlength="3">
                        </div>
                        <div class="form-actions" style="border:none; padding-top:10px; margin-top:0;">
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="form-card">
                    <h3>Cambiar Contraseña</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="password">
                        <div class="form-group">
                            <label for="current_password">Contraseña actual</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Nueva contraseña</label>
                            <input type="password" id="new_password" name="new_password" required minlength="8">
                            <small style="color:#999;">Mínimo 8 caracteres</small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmar contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="form-actions" style="border:none; padding-top:10px; margin-top:0;">
                            <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
