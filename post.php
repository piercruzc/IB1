<?php
require_once __DIR__ . '/includes/db.php';

$db = getDB();
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    header('HTTP/1.0 404 Not Found');
    header('Location: blog.html');
    exit;
}

// Fetch the post
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug
    FROM blog_posts p 
    LEFT JOIN blog_categories c ON p.category_id = c.id 
    WHERE p.slug = ? AND p.status = 'published'
");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    header('Location: blog.html');
    exit;
}

// Previous post
$prevStmt = $db->prepare("
    SELECT title, slug FROM blog_posts 
    WHERE status = 'published' AND created_at < ? 
    ORDER BY created_at DESC LIMIT 1
");
$prevStmt->execute([$post['created_at']]);
$prevPost = $prevStmt->fetch();

// Next post
$nextStmt = $db->prepare("
    SELECT title, slug FROM blog_posts 
    WHERE status = 'published' AND created_at > ? 
    ORDER BY created_at ASC LIMIT 1
");
$nextStmt->execute([$post['created_at']]);
$nextPost = $nextStmt->fetch();

// Format date
function formatDateEsFull($dateStr) {
    $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $ts = strtotime($dateStr);
    return date('d', $ts) . ' de ' . $months[(int)date('n', $ts) - 1] . ', ' . date('Y', $ts);
}

// Reading time estimate
$wordCount = str_word_count(strip_tags($post['content']));
$readTime = max(1, ceil($wordCount / 200));

// Load author settings
$settingsStmt = $db->query("SELECT setting_key, setting_value FROM blog_settings");
$authorSettings = [];
while ($row = $settingsStmt->fetch()) {
    $authorSettings[$row['setting_key']] = $row['setting_value'];
}

$aName = htmlspecialchars($authorSettings['author_name'] ?? 'IB1 Fintech');
$aBio = htmlspecialchars($authorSettings['author_bio'] ?? '');
$aAvatar = !empty($authorSettings['author_avatar']) ? htmlspecialchars($authorSettings['author_avatar']) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&h=120&fit=crop&crop=center';
$aFb = $authorSettings['author_facebook'] ?? '';
$aIg = $authorSettings['author_instagram'] ?? '';
$aLi = $authorSettings['author_linkedin'] ?? '';

$heroImage = $post['image_path'] ?: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1600&h=600&fit=crop&crop=center';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - IBM FINTECH</title>
    <meta name="description" content="<?= htmlspecialchars($post['excerpt']) ?>">
    <base href="/">
    <link rel="stylesheet" href="css/main.css?v=<?= time() ?>">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>

<body>
<!-- ===== POST HERO ===== -->
<section class="post-hero" style="background-image: url('<?= htmlspecialchars($heroImage) ?>');">
    <div class="post-hero-overlay"></div>
    <!-- Navbar -->
    <header class="post-hero-header">
        <?php include 'includes/navbar.php'; ?>
    </header>
    <div class="container">
        <div class="post-hero-content">
            <?php if ($post['category_name']): ?>
            <span class="post-hero-category"><?= htmlspecialchars($post['category_name']) ?></span>
            <?php endif; ?>

            <h1 class="post-hero-title"><?= htmlspecialchars($post['title']) ?></h1>

            <div class="post-hero-stats">
                <span class="stat-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?= $readTime ?> min de lectura
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ===== POST BODY ===== -->
<main class="post-main-v2">
    <div class="container">
        <article class="post-article-v2">

            <!-- Breadcrumb + Author -->
            <div class="post-article-header">
                <nav class="post-breadcrumb-v2">
                    <a href="index.html">Inicio</a>
                    <span>›</span>
                    <a href="blog.html">Blog</a>
                    <span>›</span>
                    <span class="current"><?= htmlspecialchars(mb_substr($post['title'], 0, 50, 'UTF-8')) ?>...</span>
                </nav>
                <div class="post-author-bar">
                    <img src="<?= $aAvatar ?>" alt="<?= $aName ?>" class="author-bar-avatar">
                    <div class="author-bar-info">
                        <span class="author-bar-name">Por <?= $aName ?></span>
                        <time class="author-bar-date" datetime="<?= date('Y-m-d', strtotime($post['created_at'])) ?>"><?= formatDateEsFull($post['created_at']) ?></time>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="post-body-v2">
                <?= $post['content'] ?>
            </div>

            <!-- Category Tag -->
            <?php if ($post['category_name']): ?>
            <div class="post-tags-v2">
                <span class="tags-icon">🏷️</span>
                <a href="search.php?cat=<?= urlencode($post['category_slug']) ?>" class="tag-v2"><?= htmlspecialchars($post['category_name']) ?></a>
            </div>
            <?php endif; ?>

        </article>

        <!-- Post Navigation -->
        <nav class="post-nav-v2">
            <?php if ($prevPost): ?>
            <a href="blog/<?= htmlspecialchars($prevPost['slug']) ?>.html" class="post-nav-card prev">
                <span class="nav-direction">← Anterior</span>
                <span class="nav-post-title"><?= htmlspecialchars($prevPost['title']) ?></span>
            </a>
            <?php else: ?>
            <div class="post-nav-card empty"></div>
            <?php endif; ?>

            <?php if ($nextPost): ?>
            <a href="blog/<?= htmlspecialchars($nextPost['slug']) ?>.html" class="post-nav-card next">
                <span class="nav-direction">Siguiente →</span>
                <span class="nav-post-title"><?= htmlspecialchars($nextPost['title']) ?></span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Author Card -->
        <section class="post-author-v2">
            <div class="author-v2-avatar">
                <img src="<?= $aAvatar ?>" alt="<?= $aName ?>">
            </div>
            <div class="author-v2-info">
                <span class="author-v2-label">Escrito por</span>
                <h3 class="author-v2-name"><?= $aName ?></h3>
                <?php if ($aBio): ?><p class="author-v2-bio"><?= $aBio ?></p><?php endif; ?>
                <div class="author-v2-social">
                    <?php if ($aFb): ?><a href="<?= htmlspecialchars($aFb) ?>" target="_blank" class="social-v2-link" title="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a><?php endif; ?>
                    <?php if ($aIg): ?><a href="<?= htmlspecialchars($aIg) ?>" target="_blank" class="social-v2-link" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a><?php endif; ?>
                    <?php if ($aLi): ?><a href="<?= htmlspecialchars($aLi) ?>" target="_blank" class="social-v2-link" title="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a><?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- CTA Section con Formulario -->
<?php include 'includes/contact_form.php'; ?>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Image Lightbox Modal -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" id="lightboxClose">&times;</button>
    <img class="lightbox-img" id="lightboxImg" alt="">
</div>

<style>
.lightbox-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    cursor: zoom-out;
}
.lightbox-overlay.active {
    display: flex;
}
.lightbox-img {
    max-width: 90vw;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.5);
    animation: lbFadeIn 0.25s ease;
}
@keyframes lbFadeIn {
    from { opacity: 0; transform: scale(0.92); }
    to { opacity: 1; transform: scale(1); }
}
.lightbox-close {
    position: absolute;
    top: 20px;
    right: 30px;
    background: none;
    border: none;
    color: white;
    font-size: 2.5rem;
    cursor: pointer;
    line-height: 1;
    z-index: 10000;
    transition: transform 0.2s;
}
.lightbox-close:hover {
    transform: scale(1.2);
}
.post-body-v2 img {
    cursor: zoom-in;
    transition: opacity 0.2s;
}
.post-body-v2 img:hover {
    opacity: 0.85;
}
</style>

<!-- Scripts -->
<script>
    const hamburger = document.querySelector('.hamburger-blue');
    const navMenu = document.querySelector('.nav-menu-blue');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.nav-dropdown');
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            if (window.innerWidth <= 768) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                });
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                    }
                });
            }
        });
        window.addEventListener('resize', function() {
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('active'));
        });

        // Lightbox
        const lb = document.getElementById('lightbox');
        const lbImg = document.getElementById('lightboxImg');
        document.querySelectorAll('.post-body-v2 img').forEach(img => {
            img.addEventListener('click', function() {
                lbImg.src = this.src;
                lb.classList.add('active');
            });
        });
        lb.addEventListener('click', function(e) {
            if (e.target !== lbImg) lb.classList.remove('active');
        });
        document.getElementById('lightboxClose').addEventListener('click', function() {
            lb.classList.remove('active');
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') lb.classList.remove('active');
        });
    });
</script>
</body>

</html>
