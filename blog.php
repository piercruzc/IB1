<?php
require_once __DIR__ . '/includes/db.php';

$db = getDB();

// Pagination
$perPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Total count of published posts
$totalStmt = $db->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
$totalPosts = (int) $totalStmt->fetchColumn();
$totalPages = max(1, ceil($totalPosts / $perPage));

// Featured post (most recent featured)
$featuredStmt = $db->query("
    SELECT p.*, c.name as category_name 
    FROM blog_posts p 
    LEFT JOIN blog_categories c ON p.category_id = c.id 
    WHERE p.status = 'published' AND p.is_featured = 1 
    ORDER BY p.created_at DESC 
    LIMIT 1
");
$featuredPost = $featuredStmt->fetch();

// Regular posts (exclude featured if on page 1)
$excludeId = ($page === 1 && $featuredPost) ? $featuredPost['id'] : 0;
$postsStmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM blog_posts p 
    LEFT JOIN blog_categories c ON p.category_id = c.id 
    WHERE p.status = 'published' AND p.id != ?
    ORDER BY p.created_at DESC 
    LIMIT ? OFFSET ?
");
$postsStmt->execute([$excludeId, $perPage, $offset]);
$posts = $postsStmt->fetchAll();

// Categories with counts
$catStmt = $db->query("
    SELECT c.name, c.slug, COUNT(p.id) as post_count 
    FROM blog_categories c 
    LEFT JOIN blog_posts p ON p.category_id = c.id AND p.status = 'published'
    GROUP BY c.id, c.name, c.slug
    HAVING post_count > 0
    ORDER BY c.name
");
$categories = $catStmt->fetchAll();

// Recent posts for sidebar
$recentStmt = $db->query("
    SELECT title, slug, image_path, created_at 
    FROM blog_posts 
    WHERE status = 'published' 
    ORDER BY created_at DESC 
    LIMIT 3
");
$recentPosts = $recentStmt->fetchAll();

// Helper: format date in Spanish
function formatDateEs($dateStr) {
    $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $ts = strtotime($dateStr);
    $day = date('d', $ts);
    $month = $months[(int)date('n', $ts) - 1];
    $year = date('Y', $ts);
    return "$day $month $year";
}

function shortDate($dateStr) {
    $months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $ts = strtotime($dateStr);
    return date('d', $ts) . ' ' . $months[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - IBM FINTECH S.A.C. | Inversiones y Finanzas</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>

<body>
<!-- Header -->
<header class="blog-header">
    <!-- Navbar con nueva estructura -->
    <?php include 'includes/navbar.php'; ?>
</header>

<!-- Blog Hero Section -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-content">
            <h1>Blog</h1>
            <p>Descubre insights sobre inversiones, fintech y el futuro de las finanzas personales</p>
        </div>
    </div>
</section>

<!-- Blog Main Content -->
<main class="blog-main">
    <div class="container">
        <div class="blog-wrapper">

            <!-- Blog Posts Grid -->
            <div class="blog-posts">

                <?php if ($page === 1 && $featuredPost): ?>
                <!-- Featured Post -->
                <article class="blog-post featured-post">
                    <div class="post-image">
                        <?php if ($featuredPost['image_path']): ?>
                            <img src="<?= htmlspecialchars($featuredPost['image_path']) ?>" alt="<?= htmlspecialchars($featuredPost['title']) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&h=400&fit=crop&crop=center" alt="Post destacado">
                        <?php endif; ?>
                        <div class="post-category">Destacado</div>
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?= formatDateEs($featuredPost['created_at']) ?></span>
                            <span class="post-author">Por <?= htmlspecialchars($featuredPost['author']) ?></span>
                        </div>
                        <h2><a href="blog/<?= htmlspecialchars($featuredPost['slug']) ?>.html"><?= htmlspecialchars($featuredPost['title']) ?></a></h2>
                        <p><?= htmlspecialchars($featuredPost['excerpt']) ?></p>
                        <a href="blog/<?= htmlspecialchars($featuredPost['slug']) ?>.html" class="read-more">Leer más →</a>
                    </div>
                </article>
                <?php endif; ?>

                <?php if (empty($posts) && !$featuredPost): ?>
                    <div style="text-align:center; padding:60px 20px; color:#6c757d;">
                        <h3>No hay artículos publicados aún</h3>
                        <p>Pronto tendremos contenido nuevo para ti.</p>
                    </div>
                <?php endif; ?>

                <!-- Regular Posts -->
                <?php foreach ($posts as $post): ?>
                <article class="blog-post">
                    <div class="post-image">
                        <?php if ($post['image_path']): ?>
                            <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=250&fit=crop&crop=center" alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php endif; ?>
                        <?php if ($post['category_name']): ?>
                            <div class="post-category"><?= htmlspecialchars($post['category_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?= formatDateEs($post['created_at']) ?></span>
                            <span class="post-author">Por <?= htmlspecialchars($post['author']) ?></span>
                        </div>
                        <h3><a href="blog/<?= htmlspecialchars($post['slug']) ?>.html"><?= htmlspecialchars($post['title']) ?></a></h3>
                        <p><?= htmlspecialchars($post['excerpt']) ?></p>
                        <a href="blog/<?= htmlspecialchars($post['slug']) ?>.html" class="read-more">Leer más →</a>
                    </div>
                </article>
                <?php endforeach; ?>

            </div>

            <!-- Blog Sidebar -->
            <aside class="blog-sidebar">

                <!-- Search Widget -->
                <div class="sidebar-widget">
                    <h4>Buscar</h4>
                    <form class="search-form" action="search.php" method="GET">
                        <input type="text" name="q" placeholder="Buscar artículos..." required>
                        <button type="submit">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Categories Widget -->
                <?php if (!empty($categories)): ?>
                <div class="sidebar-widget">
                    <h4>Categorías</h4>
                    <ul class="categories-list">
                        <?php foreach ($categories as $cat): ?>
                        <li><a href="search.php?cat=<?= urlencode($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?> <span>(<?= $cat['post_count'] ?>)</span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Recent Posts Widget -->
                <?php if (!empty($recentPosts)): ?>
                <div class="sidebar-widget">
                    <h4>Artículos Recientes</h4>
                    <div class="recent-posts">
                        <?php foreach ($recentPosts as $rp): ?>
                        <article class="recent-post">
                            <?php if ($rp['image_path']): ?>
                                <img src="<?= htmlspecialchars($rp['image_path']) ?>" alt="<?= htmlspecialchars($rp['title']) ?>">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=80&h=60&fit=crop&crop=center" alt="Post reciente">
                            <?php endif; ?>
                            <div class="recent-post-content">
                                <h5><a href="blog/<?= htmlspecialchars($rp['slug']) ?>.html"><?= htmlspecialchars($rp['title']) ?></a></h5>
                                <span class="recent-date"><?= shortDate($rp['created_at']) ?></span>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </aside>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="blog-pagination">
            <?php if ($page > 1): ?>
                <a href="blog.html?page=<?= $page - 1 ?>" class="pagination-btn prev">← Anterior</a>
            <?php else: ?>
                <button class="pagination-btn prev disabled" disabled>← Anterior</button>
            <?php endif; ?>

            <div class="pagination-numbers">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <button class="pagination-number active"><?= $i ?></button>
                    <?php else: ?>
                        <a href="blog.html?page=<?= $i ?>" class="pagination-number"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="blog.html?page=<?= $page + 1 ?>" class="pagination-btn next">Siguiente →</a>
            <?php else: ?>
                <button class="pagination-btn next disabled" disabled>Siguiente →</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</main>

<!-- CTA Section con Formulario -->
<?php include 'includes/contact_form.php'; ?>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Scripts -->
<script>
    // Hamburger Menu
    const hamburger = document.querySelector('.hamburger-blue');
    const navMenu = document.querySelector('.nav-menu-blue');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Dropdown functionality para mobile
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.nav-dropdown');

        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            // Solo agregar click listener en mobile
            if (window.innerWidth <= 768) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                });

                // Cerrar dropdown al hacer click fuera
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                    }
                });
            }
        });

        // Search form
        document.querySelector('.search-form')?.addEventListener('submit', function (e) {
            // Let the form submit naturally to search.php
        });

        // Newsletter form
        document.querySelector('.newsletter-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            console.log('Suscribiendo email:', email);
            alert('¡Gracias por suscribirte!');
            this.reset();
        });

        // Re-evaluar en resize
        window.addEventListener('resize', function() {
            const dropdowns = document.querySelectorAll('.nav-dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        });
    });
</script>
</body>

</html>
