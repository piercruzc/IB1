<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();
$errors = [];
$success = false;

// Load current settings
function getSettings($db) {
    $stmt = $db->query("SELECT setting_key, setting_value FROM blog_settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

$settings = getSettings($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['author_name', 'author_bio', 'author_facebook', 'author_instagram', 'author_linkedin'];

    foreach ($fields as $key) {
        $value = trim($_POST[$key] ?? '');
        $stmt = $db->prepare("INSERT INTO blog_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }

    // Handle avatar upload
    if (isset($_FILES['author_avatar']) && $_FILES['author_avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['author_avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts) || $file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Avatar: solo JPG, PNG, WebP (máx 2MB).';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedTypes)) {
                $errors[] = 'Tipo de archivo no válido.';
            } else {
                // Delete old avatar
                $oldAvatar = $settings['author_avatar'] ?? '';
                if ($oldAvatar && file_exists(__DIR__ . '/../' . $oldAvatar)) {
                    unlink(__DIR__ . '/../' . $oldAvatar);
                }

                $newName = 'author_' . uniqid() . '.' . $ext;
                $dest = __DIR__ . '/../uploads/blog/' . $newName;
                move_uploaded_file($file['tmp_name'], $dest);

                $avatarPath = 'uploads/blog/' . $newName;
                $stmt = $db->prepare("INSERT INTO blog_settings (setting_key, setting_value) VALUES ('author_avatar', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$avatarPath, $avatarPath]);
            }
        }
    }

    if (empty($errors)) {
        $success = true;
        $settings = getSettings($db); // Reload
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin Blog</title>
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
                <a href="settings.php" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Configuración
                </a>
            </nav>
            <div class="sidebar-footer">
                <span class="admin-user"><?= htmlspecialchars($_SESSION['admin_user']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Configuración del Autor</h1>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">Configuración guardada exitosamente.</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="post-form">
                <div class="form-grid">
                    <div class="form-main">
                        <div class="form-card">
                            <h3>Información del Autor</h3>
                            <p style="color:#6c757d; margin-bottom:20px; font-size:14px;">Esta información aparece al final de cada post publicado.</p>

                            <div class="form-group">
                                <label for="author_name">Nombre del Autor</label>
                                <input type="text" id="author_name" name="author_name"
                                       value="<?= htmlspecialchars($settings['author_name'] ?? 'IB1 Fintech') ?>">
                            </div>

                            <div class="form-group">
                                <label for="author_bio">Biografía / Descripción</label>
                                <textarea id="author_bio" name="author_bio" rows="5"><?= htmlspecialchars($settings['author_bio'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-card">
                            <h3>Redes Sociales</h3>
                            <div class="form-group">
                                <label for="author_facebook">Facebook URL</label>
                                <input type="url" id="author_facebook" name="author_facebook"
                                       value="<?= htmlspecialchars($settings['author_facebook'] ?? '') ?>"
                                       placeholder="https://facebook.com/...">
                            </div>
                            <div class="form-group">
                                <label for="author_instagram">Instagram URL</label>
                                <input type="url" id="author_instagram" name="author_instagram"
                                       value="<?= htmlspecialchars($settings['author_instagram'] ?? '') ?>"
                                       placeholder="https://instagram.com/...">
                            </div>
                            <div class="form-group">
                                <label for="author_linkedin">LinkedIn URL</label>
                                <input type="url" id="author_linkedin" name="author_linkedin"
                                       value="<?= htmlspecialchars($settings['author_linkedin'] ?? '') ?>"
                                       placeholder="https://linkedin.com/...">
                            </div>
                        </div>
                    </div>

                    <div class="form-sidebar">
                        <div class="form-card">
                            <h3>Avatar del Autor</h3>
                            <div class="form-group">
                                <?php
                                    $avatar = $settings['author_avatar'] ?? '';
                                    $avatarSrc = $avatar ? '../' . htmlspecialchars($avatar) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&h=120&fit=crop&crop=center';
                                ?>
                                <div style="text-align:center; margin-bottom:15px;">
                                    <img src="<?= $avatarSrc ?>" alt="Avatar" id="avatarPreview"
                                         style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #e9ecef;">
                                </div>
                                <div class="image-upload-area">
                                    <input type="file" id="author_avatar" name="author_avatar"
                                           accept="image/jpeg,image/png,image/webp"
                                           onchange="previewAvatar(this)">
                                    <div class="upload-placeholder">
                                        <span>Cambiar avatar</span>
                                        <small>JPG, PNG, WebP (máx 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-card">
                            <h3>Vista Previa</h3>
                            <div style="background:#f8f9fa; border-radius:10px; padding:15px; text-align:center;">
                                <img src="<?= $avatarSrc ?>" alt="Preview" id="previewImg"
                                     style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:8px;">
                                <h4 style="margin:0 0 5px; font-size:14px;" id="previewName"><?= htmlspecialchars($settings['author_name'] ?? 'IB1 Fintech') ?></h4>
                                <p style="margin:0; font-size:12px; color:#6c757d; line-height:1.4;" id="previewBio">
                                    <?= htmlspecialchars(mb_substr($settings['author_bio'] ?? '', 0, 100, 'UTF-8')) ?>...
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Configuración</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                    document.getElementById('previewImg').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Live preview for name and bio
        document.getElementById('author_name').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value || 'IB1 Fintech';
        });
        document.getElementById('author_bio').addEventListener('input', function() {
            const text = this.value.substring(0, 100);
            document.getElementById('previewBio').textContent = text + (this.value.length > 100 ? '...' : '');
        });
    </script>
</body>
</html>
