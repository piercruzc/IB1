<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();
$errors = [];
$editMode = false;
$editCat = null;

// Handle DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = (int) $_POST['delete_id'];
    // Check if category has posts
    $check = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
    $check->execute([$delId]);
    if ((int)$check->fetchColumn() > 0) {
        $errors[] = 'No se puede eliminar una categoría que tiene posts asociados. Reasigne los posts primero.';
    } else {
        $db->prepare("DELETE FROM blog_categories WHERE id = ?")->execute([$delId]);
        header('Location: categories.php?msg=deleted');
        exit;
    }
}

// Handle CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if (!$name) {
        $errors[] = 'El nombre es obligatorio.';
    } else {
        if (!$slug) $slug = createSlug($name);
        $check = $db->prepare("SELECT id FROM blog_categories WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) {
            $errors[] = 'Ya existe una categoría con ese slug.';
        } else {
            $db->prepare("INSERT INTO blog_categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
            header('Location: categories.php?msg=created');
            exit;
        }
    }
}

// Handle UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $editId = (int) ($_POST['edit_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if (!$name) {
        $errors[] = 'El nombre es obligatorio.';
    } else {
        if (!$slug) $slug = createSlug($name);
        $check = $db->prepare("SELECT id FROM blog_categories WHERE slug = ? AND id != ?");
        $check->execute([$slug, $editId]);
        if ($check->fetch()) {
            $errors[] = 'Ya existe otra categoría con ese slug.';
        } else {
            $db->prepare("UPDATE blog_categories SET name = ?, slug = ? WHERE id = ?")->execute([$name, $slug, $editId]);
            header('Location: categories.php?msg=updated');
            exit;
        }
    }
}

// Check if editing
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
    $stmt->execute([$editId]);
    $editCat = $stmt->fetch();
    if ($editCat) $editMode = true;
}

// Fetch all categories with post counts
$categories = $db->query("
    SELECT c.*, COUNT(p.id) as post_count 
    FROM blog_categories c 
    LEFT JOIN blog_posts p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.name
")->fetchAll();

$msg = $_GET['msg'] ?? '';

function createSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $replacements = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u'];
    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Admin Blog</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="css/admin.css">
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
                <a href="create.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nuevo Post
                </a>
                <a href="categories.php" class="nav-item active">
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
            </nav>
            <div class="sidebar-footer">
                <span class="admin-user"><?= htmlspecialchars($_SESSION['admin_user']) ?></span>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Categorías</h1>
            </div>

            <?php if ($msg === 'created'): ?>
                <div class="alert alert-success">Categoría creada exitosamente.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="alert alert-success">Categoría actualizada exitosamente.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert alert-success">Categoría eliminada exitosamente.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="categories-layout">
                <!-- Create / Edit Form -->
                <div class="form-card category-form-card">
                    <h3><?= $editMode ? 'Editar Categoría' : 'Nueva Categoría' ?></h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
                        <?php if ($editMode): ?>
                            <input type="hidden" name="edit_id" value="<?= $editCat['id'] ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="name">Nombre *</label>
                            <input type="text" id="name" name="name" required
                                   value="<?= htmlspecialchars($editMode ? $editCat['name'] : ($_POST['name'] ?? '')) ?>"
                                   oninput="generateCatSlug(this.value)">
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($editMode ? $editCat['slug'] : ($_POST['slug'] ?? '')) ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <div class="form-actions" style="border:none; padding-top:10px; margin-top:0;">
                            <button type="submit" class="btn btn-primary"><?= $editMode ? 'Guardar Cambios' : 'Crear Categoría' ?></button>
                            <?php if ($editMode): ?>
                                <a href="categories.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Categories Table -->
                <div class="posts-table-wrapper">
                    <table class="posts-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Posts</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="4" class="empty-state">No hay categorías creadas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                    <td><small class="slug-text"><?= htmlspecialchars($cat['slug']) ?></small></td>
                                    <td><?= $cat['post_count'] ?></td>
                                    <td class="col-actions">
                                        <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-edit" title="Editar">✏️</a>
                                        <?php if ($cat['post_count'] == 0): ?>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                            <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-delete" title="Eliminar">🗑️</button>
                                        </form>
                                        <?php else: ?>
                                        <span class="btn btn-sm" style="opacity:0.3; cursor:not-allowed;" title="Tiene posts asociados">🗑️</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function generateCatSlug(text) {
            const slug = text.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-|-$/g, '');
            document.getElementById('slug').value = slug;
        }
    </script>
</body>
</html>
