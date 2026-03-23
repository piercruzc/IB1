<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();
$errors = [];

// Fetch categories
$categories = $db->query("SELECT id, name FROM blog_categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $content  = $_POST['content'] ?? '';
    $category = (int) ($_POST['category_id'] ?? 0);
    $featured = isset($_POST['is_featured']) ? 1 : 0;
    $status   = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';
    $author   = 'IB1 Fintech';

    // Auto-generate excerpt from content (strip HTML, first 200 chars)
    $excerpt = mb_substr(strip_tags($content), 0, 200, 'UTF-8');
    if (mb_strlen(strip_tags($content), 'UTF-8') > 200) {
        $excerpt .= '...';
    }

    // Validation
    if (!$title) $errors[] = 'El título es obligatorio.';
    if (!$content) $errors[] = 'El contenido es obligatorio.';
    if (!$slug) {
        $slug = createSlug($title);
    }

    // Check slug uniqueness
    $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $errors[] = 'El slug ya existe. Use uno diferente.';
    }

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['image']);
        if ($uploadResult['error']) {
            $errors[] = $uploadResult['error'];
        } else {
            $imagePath = $uploadResult['path'];
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO blog_posts (title, slug, excerpt, content, image_path, category_id, author, is_featured, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $slug, $excerpt, $content, $imagePath, $category ?: null, $author, $featured, $status]);

        header('Location: index.php?msg=created');
        exit;
    }
}

function createSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $replacements = [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        'ñ'=>'n','ü'=>'u','ä'=>'a','ö'=>'o'
    ];
    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function handleImageUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize      = 5 * 1024 * 1024;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['error' => 'Extensión no permitida. Use: jpg, jpeg, png, webp.', 'path' => ''];
    }
    if ($file['size'] > $maxSize) {
        return ['error' => 'El archivo es demasiado grande. Máximo 5MB.', 'path' => ''];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Tipo de archivo no permitido.', 'path' => ''];
    }
    $newName = uniqid('blog_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/blog/';
    $destination = $uploadDir . $newName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['error' => 'Error al subir el archivo.', 'path' => ''];
    }
    return ['error' => null, 'path' => 'uploads/blog/' . $newName];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Post - Admin Blog</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.tiny.cloud/1/<?= htmlspecialchars(getenv('TINYMCE_API_KEY') ?: 'no-api-key') ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
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
                <a href="create.php" class="nav-item active">
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
                <a href="account.php" class="nav-item">
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

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Nuevo Post</h1>
                <a href="index.php" class="btn btn-secondary">← Volver</a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="post-form">
                <div class="form-grid">
                    <div class="form-main">
                        <div class="form-group">
                            <label for="title">Título *</label>
                            <input type="text" id="title" name="title" required
                                   value="<?= htmlspecialchars($title ?? '') ?>"
                                   oninput="generateSlug(this.value)">
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug (URL)</label>
                            <div class="slug-preview">
                                <span class="slug-prefix">/blog/</span>
                                <input type="text" id="slug" name="slug"
                                       value="<?= htmlspecialchars($slug ?? '') ?>"
                                       placeholder="se-genera-automaticamente">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content">Contenido *</label>
                            <textarea id="content" name="content"><?= htmlspecialchars($content ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-sidebar">
                        <div class="form-card">
                            <h3>Publicación</h3>
                            <div class="form-group">
                                <label for="status">Estado</label>
                                <select id="status" name="status">
                                    <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Borrador</option>
                                    <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                                </select>
                            </div>
                            <div class="form-group checkbox-group">
                                <label>
                                    <input type="checkbox" name="is_featured" value="1"
                                           <?= !empty($featured) ? 'checked' : '' ?>>
                                    Post destacado
                                </label>
                            </div>
                        </div>

                        <div class="form-card">
                            <h3>Categoría</h3>
                            <div class="form-group">
                                <select id="category_id" name="category_id">
                                    <option value="">Sin categoría</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($category ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-card">
                            <h3>Imagen Destacada</h3>
                            <div class="form-group">
                                <div class="image-upload-area" id="imageUploadArea">
                                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                                           onchange="previewImage(this)">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <path d="M21 15L16 10L5 21"/>
                                        </svg>
                                        <span>Click para subir imagen</span>
                                        <small>JPG, PNG, WebP (máx 5MB)</small>
                                    </div>
                                    <img id="imagePreview" class="image-preview" style="display:none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Publicar Post</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        // TinyMCE Editor
        tinymce.init({
            selector: '#content',
            height: 500,
            language: 'es',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            menubar: 'file edit view insert format tools table',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 16px; line-height: 1.6; }',
            branding: false,
            promotion: false,
            skin: 'oxide',
            content_css: 'default',
            // Image upload
            images_upload_url: 'upload_image.php',
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/jpeg,image/png,image/webp,image/gif');
                input.onchange = function() {
                    var file = this.files[0];
                    var formData = new FormData();
                    formData.append('file', file);
                    fetch('upload_image.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.location) cb(data.location, { title: file.name });
                            else alert(data.error || 'Error al subir imagen');
                        })
                        .catch(() => alert('Error de conexión'));
                };
                input.click();
            }
        });

        function generateSlug(text) {
            const slug = text.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-|-$/g, '');
            document.getElementById('slug').value = slug;
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('uploadPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
