<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

// Handle delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    
    // Get image path before deleting
    $stmt = $db->prepare("SELECT image_path FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if ($post && $post['image_path']) {
        $imagePath = __DIR__ . '/../' . $post['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: index.php?msg=deleted');
    exit;
}

// Fetch all posts
$stmt = $db->query("
    SELECT p.*, c.name as category_name 
    FROM blog_posts p 
    LEFT JOIN blog_categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Blog IBM FINTECH</title>
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
                <a href="index.php" class="nav-item active">
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
                <h1>Gestión de Posts</h1>
                <a href="create.php" class="btn btn-primary">+ Nuevo Post</a>
            </div>

            <?php if ($msg === 'created'): ?>
                <div class="alert alert-success">Post creado exitosamente.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="alert alert-success">Post actualizado exitosamente.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert alert-success">Post eliminado exitosamente.</div>
            <?php endif; ?>

            <div class="posts-table-wrapper">
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Destacado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    No hay posts creados. <a href="create.php">Crear el primero</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td class="col-image">
                                        <?php if ($post['image_path']): ?>
                                            <img src="../<?= htmlspecialchars($post['image_path']) ?>" alt="" class="table-thumb">
                                        <?php else: ?>
                                            <div class="table-thumb placeholder">Sin img</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-title">
                                        <strong><?= htmlspecialchars($post['title']) ?></strong>
                                        <br><small class="slug-text">/blog/<?= htmlspecialchars($post['slug']) ?>.html</small>
                                    </td>
                                    <td><?= htmlspecialchars($post['category_name'] ?? 'Sin categoría') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $post['status'] === 'published' ? 'success' : 'warning' ?>">
                                            <?= $post['status'] === 'published' ? 'Publicado' : 'Borrador' ?>
                                        </span>
                                    </td>
                                    <td><?= $post['is_featured'] ? '⭐' : '—' ?></td>
                                    <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                                    <td class="col-actions">
                                        <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-edit" title="Editar">✏️</a>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('¿Eliminar este post?');">
                                            <input type="hidden" name="delete_id" value="<?= $post['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-delete" title="Eliminar">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
