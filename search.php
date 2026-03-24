<?php
require_once __DIR__ . '/includes/db.php';

$db = getDB();

$query = trim($_GET['q'] ?? '');
$catFilter = trim($_GET['cat'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

$results = [];
$totalResults = 0;

// Build the search query
$whereClause = "WHERE p.status = 'published'";
$params = [];

if ($query) {
    $whereClause .= " AND (p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)";
    $searchTerm = '%' . $query . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($catFilter) {
    $whereClause .= " AND c.slug = ?";
    $params[] = $catFilter;
}

// Count total
$countSql = "SELECT COUNT(*) FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id $whereClause";
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalResults = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($totalResults / $perPage));

// Fetch results
$sql = "
    SELECT p.*, c.name as category_name, c.slug as category_slug
    FROM blog_posts p 
    LEFT JOIN blog_categories c ON p.category_id = c.id 
    $whereClause
    ORDER BY p.created_at DESC 
    LIMIT $perPage OFFSET $offset
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Get category name for filter display
$catName = '';
if ($catFilter) {
    $catStmt = $db->prepare("SELECT name FROM blog_categories WHERE slug = ?");
    $catStmt->execute([$catFilter]);
    $catName = $catStmt->fetchColumn() ?: $catFilter;
}

// Categories for filter dropdown
$allCats = $db->query("SELECT name, slug FROM blog_categories ORDER BY name")->fetchAll();

// Helper
function formatDateSearch($dateStr) {
    $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $ts = strtotime($dateStr);
    return date('d', $ts) . ' ' . $months[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

// Build query string for pagination
function buildSearchUrl($params) {
    return 'search.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda - IBM FINTECH S.A.C.</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>

<body>
    <!-- Header -->
    <header class="blog-header">
        <!-- Navbar con logo clickeable -->
        <?php include 'includes/navbar.php'; ?>
    </header>

    <!-- Search Results Hero Section -->
    <section class="search-hero">
        <div class="container">
            <div class="search-hero-content">
                <h1>Resultados de búsqueda</h1>
                <div class="search-info">
                    <?php if ($query): ?>
                        <p>Resultados para: <span class="search-query">"<?= htmlspecialchars($query) ?>"</span></p>
                    <?php elseif ($catName): ?>
                        <p>Categoría: <span class="search-query">"<?= htmlspecialchars($catName) ?>"</span></p>
                    <?php endif; ?>
                    <p class="search-stats">
                        <?php if ($totalResults > 0): ?>
                            Encontramos <strong><?= $totalResults ?> artículo<?= $totalResults !== 1 ? 's' : '' ?></strong> relacionados
                        <?php else: ?>
                            No se encontraron resultados
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results Main Content -->
    <main class="search-main">
        <div class="container">

            <!-- Search Bar Top -->
            <div class="search-bar-top">
                <form class="search-form-main" method="GET" action="search.php">
                    <input type="text" name="q" placeholder="Buscar artículos..." value="<?= htmlspecialchars($query) ?>" required>
                    <?php if ($catFilter): ?>
                        <input type="hidden" name="cat" value="<?= htmlspecialchars($catFilter) ?>">
                    <?php endif; ?>
                    <button type="submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </button>
                </form>
                <div class="search-filters">
                    <span>Filtrar por:</span>
                    <form method="GET" action="search.php" id="filterForm">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">
                        <select class="filter-category" name="cat" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($allCats as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $catFilter === $cat['slug'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (!empty($results)): ?>
            <!-- Results Grid -->
            <div class="results-grid">
                <?php foreach ($results as $result): ?>
                <article class="search-result-card">
                    <div class="result-image">
                        <?php if ($result['image_path']): ?>
                            <img src="<?= htmlspecialchars($result['image_path']) ?>" alt="<?= htmlspecialchars($result['title']) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=250&fit=crop&crop=center" alt="<?= htmlspecialchars($result['title']) ?>">
                        <?php endif; ?>
                        <?php if ($result['category_name']): ?>
                            <div class="result-category"><?= htmlspecialchars($result['category_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="result-content">
                        <div class="result-meta">
                            <span class="result-date"><?= formatDateSearch($result['created_at']) ?></span>
                            <span class="result-author">Por <?= htmlspecialchars($result['author']) ?></span>
                        </div>
                        <h3><a href="blog/<?= htmlspecialchars($result['slug']) ?>.html"><?= htmlspecialchars($result['title']) ?></a></h3>
                        <p><?= htmlspecialchars(html_entity_decode($result['excerpt'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <a href="blog/<?= htmlspecialchars($result['slug']) ?>.html" class="result-read-more">Leer más →</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Search Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="search-pagination">
                <?php
                    $paginationParams = [];
                    if ($query) $paginationParams['q'] = $query;
                    if ($catFilter) $paginationParams['cat'] = $catFilter;
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?= buildSearchUrl(array_merge($paginationParams, ['page' => $page - 1])) ?>" class="pagination-btn prev">← Anterior</a>
                <?php else: ?>
                    <button class="pagination-btn prev disabled" disabled>← Anterior</button>
                <?php endif; ?>
                
                <div class="pagination-numbers">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <button class="pagination-number active"><?= $i ?></button>
                        <?php else: ?>
                            <a href="<?= buildSearchUrl(array_merge($paginationParams, ['page' => $i])) ?>" class="pagination-number"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                
                <?php if ($page < $totalPages): ?>
                    <a href="<?= buildSearchUrl(array_merge($paginationParams, ['page' => $page + 1])) ?>" class="pagination-btn next">Siguiente →</a>
                <?php else: ?>
                    <button class="pagination-btn next disabled" disabled>Siguiente →</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <!-- No Results Message -->
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h3>No encontramos resultados</h3>
                <p>Lo sentimos, no encontramos artículos relacionados con tu búsqueda.</p>
                <div class="no-results-suggestions">
                    <h4>Sugerencias:</h4>
                    <ul>
                        <li>Verifica la ortografía de las palabras</li>
                        <li>Usa términos más generales</li>
                        <li>Prueba con sinónimos</li>
                        <li>Reduce el número de palabras</li>
                    </ul>
                </div>
                <a href="blog.html" class="btn-back-blog">Ver todos los artículos</a>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- CTA Section - SIN ESPACIO CON FOOTER -->
    <section class="cta-modern cta-no-margin">
        <div class="container">
            <div class="cta-content-modern">
                <div class="cta-text">
                    <h2>¿Listo para hacer crecer tu patrimonio?</h2>
                    <p>El futuro financiero de tu familia empieza hoy. No lo pospongas, decide con claridad y confianza.
                    </p>
                    <div class="cta-features">
                        <div class="cta-feature">
                            <span class="feature-check">✓</span>
                            <span>Contratos legalizados</span>
                        </div>
                        <div class="cta-feature">
                            <span class="feature-check">✓</span>
                            <span>Rendimientos garantizados</span>
                        </div>
                        <div class="cta-feature">
                            <span class="feature-check">✓</span>
                            <span>Flexibilidad total</span>
                        </div>
                    </div>
                </div>
                <div class="cta-action">
                    <a href="index.php#contacto" class="btn btn-primary btn-large">Comenzar mi inversión</a>
                    <p class="cta-note">* Desde S/ 500 soles</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - PEGADO AL CTA -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script>
        // Mobile menu
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
        });
    </script>
</body>

</html>