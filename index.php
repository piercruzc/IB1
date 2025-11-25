<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBM FINTECH S.A.C. | Inversiones Digitales de Confianza</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>

<body>
    <!-- Hero Final -->
    <header class="hero-final">
        <!-- Navbar con dropdowns (Desktop) + menú plano (Móvil) -->
        <?php include 'includes/navbar.php'; ?>

        <!-- Slider Final -->
        <div class="final-slider-container">
            <div class="slider-wrapper-final">

                <!-- Slide 1 -->
                <div class="final-slide active" data-slide="1">
                    <img src="img/slide1.jpg" alt="El futuro de tu familia" class="slide-image-final">
                    <div class="slide-shadow-overlay"></div>
                </div>

                <!-- Slide 2 -->
                <div class="final-slide" data-slide="2">
                    <img src="img/slide3.jpg" alt="Conviértete en inversionista" class="slide-image-final">
                    <div class="slide-shadow-overlay"></div>
                </div>

                <!-- Slide 3 -->
                <div class="final-slide" data-slide="3">
                    <img src="img/slide2.jpg" alt="Trabaja tu dinero" class="slide-image-final">
                    <div class="slide-shadow-overlay"></div>
                </div>
            </div>

            <!-- Flechas -->
            <button class="slider-arrow-final slider-left-final" id="prevBtn">‹</button>
            <button class="slider-arrow-final slider-right-final" id="nextBtn">›</button>

            <!-- Mensaje con botón -->
            <div class="slide-message-bottom slide-message-right">
                <div class="message-content-bottom">
                    <h3 class="message-title-bottom">El futuro de tu familia merece más que intereses mínimos.</h3>
                    <p class="message-description-bottom">Haz que tu patrimonio trabaje tan duro como tú.</p>
                    <div class="message-button-center">
                        <a href="#contacto" class="message-btn-simple" data-slide="1">Contactar asesor</a>
                        <a href="#contacto" class="message-btn-simple" data-slide="2" style="display:none;">Comenzar
                            ahora</a>
                        <a href="#contacto" class="message-btn-simple" data-slide="3" style="display:none;">Conoce
                            más</a>
                    </div>
                </div>
            </div>

            <!-- Dots -->
            <div class="final-dots-container">
                <button class="final-dot active" data-slide="1"></button>
                <button class="final-dot" data-slide="2"></button>
                <button class="final-dot" data-slide="3"></button>
            </div>
        </div>
    </header>

    <!-- Nueva Sección Principal -->
    <section class="main-intro-section">
        <div class="container">
            <div class="intro-content">
                <h2>Tú eliges cómo crecer. Nosotros te guiamos en el camino.</h2>
            </div>
        </div>
    </section>

    <!-- Nueva Sección de Cards de Servicios -->
    <section class="services-overview">
        <div class="container">
            <div class="services-cards-grid">
                <!-- Card 1: Consultoría Financiera -->
                <a href="consultoria_financiera.html" class="service-overview-card">
                    <div class="service-card-icon">
                        <img src="img/asesoria_personalizada.png" alt="Consultoría y Asesoría Financiera"
                            class="service-icon-svg">
                    </div>
                    <h3>Consultoría y Asesoría Financiera</h3>
                </a>

                <!-- Card 2: Gestión Patrimonial -->
                <a href="gestion_patrimonial.html" class="service-overview-card">
                    <div class="service-card-icon">
                        <img src="img/gestion_patrimonial.png" alt="Gestión Patrimonial" class="service-icon-svg">
                    </div>
                    <h3>Gestión Patrimonial</h3>
                </a>

                <!-- Card 3: IBM Academy -->
                <a href="ibm_academy.html" class="service-overview-card">
                    <div class="service-card-icon">
                        <img src="img/educacion_financiera.png" alt="Educación Financiera" class="service-icon-svg">
                    </div>
                    <h3>Educación y Formación Financiera</h3>
                </a>

                <!-- Card 4: Planes de Inversión -->
                <a href="planes.html" class="service-overview-card">
                    <div class="service-card-icon">
                        <img src="img/inversiones_inteligentes.png" alt="Inversiones Inteligentes"
                            class="service-icon-svg">
                    </div>
                    <h3>Inversiones Inteligentes</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Primer Banner Azul CTA -->
    <section class="blue-banner-cta">
        <div class="container">
            <div class="banner-wrapper">
                <div class="banner-content">
                    <div class="banner-icon">
                        <img src="img/banner1.png" alt="Haz que tu dinero trabaje por ti">
                    </div>
                    <div class="banner-text">
                        <h2>Haz que tu dinero trabaje por ti. Nosotros te mostramos cómo.</h2>
                        <p>Conoce las soluciones que más se ajustan para ti, y comienza el camino a tu libertad
                            financiera hoy.</p>
                    </div>
                    <div class="banner-cta">
                        <a href="planes.html" class="banner-btn">Comienza aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVA SECCIÓN DE TEXTO -->
    <section class="situations-section">
        <div class="container">
            <div class="situations-content">
                <h2>Situaciones reales, oportunidades a medida.</h2>
            </div>
        </div>
    </section>

    <!-- NUEVA SECCIÓN DE POSTS/ARTÍCULOS CON SLIDER -->
    <section class="blog-preview-section">
        <div class="container">
            <div class="blog-slider-container">
                <div class="blog-slider-wrapper">
                    <div class="blog-posts-slider" id="blogSlider">
                        <!-- Post 1 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=400&h=200&fit=crop&crop=center"
                                    alt="Inversiones inteligentes">
                            </div>
                            <div class="blog-card-content">
                                <h3>5 Estrategias de Inversión para Maximizar tus Rendimientos en 2024</h3>
                                <p>Descubre las mejores estrategias que están utilizando los inversionistas exitosos
                                    para hacer crecer su patrimonio en el mercado actual.</p>
                            </div>
                        </article>

                        <!-- Post 2 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=200&fit=crop&crop=center"
                                    alt="Gestión patrimonial">
                            </div>
                            <div class="blog-card-content">
                                <h3>Gestión Patrimonial: Protege y Haz Crecer tu Patrimonio Familiar</h3>
                                <p>Aprende cómo una gestión patrimonial profesional puede proteger y multiplicar la
                                    riqueza de tu familia a largo plazo.</p>
                            </div>
                        </article>

                        <!-- Post 3 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=200&fit=crop&crop=center"
                                    alt="Educación financiera">
                            </div>
                            <div class="blog-card-content">
                                <h3>Educación Financiera: El Primer Paso hacia la Libertad Económica</h3>
                                <p>La educación financiera es la base del éxito económico. Te mostramos cómo empezar tu
                                    camino hacia la independencia financiera.</p>
                            </div>
                        </article>

                        <!-- Post 4 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=400&h=200&fit=crop&crop=center"
                                    alt="Criptomonedas">
                            </div>
                            <div class="blog-card-content">
                                <h3>Criptomonedas: Guía Completa para Inversionistas Principiantes</h3>
                                <p>Todo lo que necesitas saber antes de invertir en activos digitales. Una guía práctica
                                    y segura para diversificar tu portafolio.</p>
                            </div>
                        </article>

                        <!-- Post 5 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=400&h=200&fit=crop&crop=center"
                                    alt="Planificación financiera">
                            </div>
                            <div class="blog-card-content">
                                <h3>Planificación Financiera Personal: Construye tu Futuro Económico</h3>
                                <p>Descubre cómo crear un plan financiero sólido que te permita alcanzar tus metas y
                                    objetivos económicos a corto y largo plazo.</p>
                            </div>
                        </article>

                        <!-- Post 6 -->
                        <article class="blog-preview-card">
                            <div class="blog-card-image">
                                <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400&h=200&fit=crop&crop=center"
                                    alt="Mercados financieros">
                            </div>
                            <div class="blog-card-content">
                                <h3>Análisis de Mercados: Tendencias y Oportunidades de Inversión 2024</h3>
                                <p>Un análisis detallado de las tendencias actuales del mercado financiero y las mejores
                                    oportunidades de inversión disponibles.</p>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Controles del slider -->
                <div class="blog-slider-navigation">
                    <div class="blog-slider-controls">
                        <button class="blog-slider-btn blog-prev" id="blogPrev">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button class="blog-slider-btn blog-next" id="blogNext">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Indicadores -->
                    <div class="blog-slider-dots">
                        <button class="blog-dot active" data-slide="0"></button>
                        <button class="blog-dot" data-slide="1"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO SEGUNDO BANNER AZUL CTA -->
    <section class="blog-banner-cta">
        <div class="container">
            <div class="banner-wrapper">
                <div class="banner-content">
                    <div class="banner-icon">
                        <img src="img/banner2.png" alt="Visita nuestro Blog">
                    </div>
                    <div class="banner-text">
                        <h2>El futuro financiero de tu familia empieza hoy.</h2>
                        <p>Infórmate, analiza y actúa con contenido diseñado por expertos en inversión y finanzas
                            digitales.</p>
                    </div>
                    <div class="banner-cta">
                        <a href="blog.html" class="banner-btn">Visita nuestro Blog</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonios" class="testimonials">
        <div class="container">
            <div class="testimonials-content">
                <h2>Qué dicen nuestros clientes. Historias de éxito que nos motivan cada día.</h2>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <div class="quote-icon">"</div>
                        <p>"Con IBM FinTech mi dinero por fin trabaja para mí. Los rendimientos son exactamente como
                            prometieron y la transparencia es total."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/80/80?random=7" alt="María González" class="author-avatar">
                        <div class="author-info">
                            <h4>María González</h4>
                            <span>Empresaria, Lima</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <div class="quote-icon">"</div>
                        <p>"Después de años con depósitos a plazo fijo que no me daban nada, decidí invertir con IBM
                            FinTech. Mejor decisión financiera que he tomado."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/80/80?random=8" alt="Carlos Mendoza" class="author-avatar">
                        <div class="author-info">
                            <h4>Carlos Mendoza</h4>
                            <span>Ingeniero, Arequipa</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <div class="quote-icon">"</div>
                        <p>"La flexibilidad de poder retirar mi dinero cuando lo necesito y los reportes mensuales me
                            dan total confianza en mi inversión."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://picsum.photos/80/80?random=9" alt="Ana Vargas" class="author-avatar">
                        <div class="author-info">
                            <h4>Ana Vargas</h4>
                            <span>Doctora, Cusco</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section con Formulario -->
    <?php include 'includes/contact_form.php'; ?>

    <!-- Nueva Sección de Cierre -->
    <section class="closing-section">
        <div class="container">
            <div class="closing-content">
                <h2>Más que una Fintech, somos tu aliado.</h2>
            </div>

            <div class="closing-info-container">
                <div class="closing-image">
                    <img src="img/empieza_tu_camino.jpg" alt="Equipo IBM FinTech">
                </div>

                <div class="closing-text">
                    <h3>Empieza tu camino con nosotros</h3>

                    <p>En el Perú, millones de personas confían sus ahorros a los bancos, pero las bajas rentabilidades
                        y la inflación hacen que su dinero pierda valor con el tiempo.</p>

                    <p>En IBM FinTech creemos que tu esfuerzo merece más. Transformamos el ahorro tradicional en
                        inversión inteligente, combinando asesoría financiera, tecnología y gestión profesional de
                        patrimonio.</p>

                    <p>Te acompañamos para que tu capital no solo se conserve, sino que crezca con propósito, seguridad
                        y visión de futuro.</p>

                    <div class="closing-cta">
                        <a href="nosotros.html" class="closing-btn">Conócenos más</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts - MENÚ MÓVIL SIMPLE + SLIDERS -->
    <script>
        (function () {
            'use strict';

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            function init() {
                // ===== MENÚ MÓVIL SIMPLE =====
                const hamburger = document.querySelector('.hamburger-blue');
                const navMenu = document.querySelector('.nav-menu-blue');

                if (hamburger && navMenu) {
                    // Toggle menú
                    hamburger.onclick = function () {
                        this.classList.toggle('active');
                        navMenu.classList.toggle('active');
                    };

                    // Cerrar al hacer click en cualquier link
                    const links = navMenu.querySelectorAll('a');
                    links.forEach(function (link) {
                        link.onclick = function () {
                            if (window.innerWidth <= 768) {
                                hamburger.classList.remove('active');
                                navMenu.classList.remove('active');
                            }
                        };
                    });

                    // Cerrar al hacer click fuera
                    document.onclick = function (e) {
                        if (window.innerWidth <= 768 && !e.target.closest('.navbar-blue')) {
                            hamburger.classList.remove('active');
                            navMenu.classList.remove('active');
                        }
                    };
                }

                // ===== SLIDER HERO =====
                const slides = document.querySelectorAll('.final-slide');
                const dots = document.querySelectorAll('.final-dot');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                let current = 0;

                function showSlide(n) {
                    slides.forEach(s => s.classList.remove('active'));
                    dots.forEach(d => d.classList.remove('active'));
                    slides[n].classList.add('active');
                    dots[n].classList.add('active');
                    current = n;

                    document.querySelectorAll('.message-btn-simple').forEach(btn => btn.style.display = 'none');
                    const activeBtn = document.querySelector('.message-btn-simple[data-slide="' + (n + 1) + '"]');
                    if (activeBtn) activeBtn.style.display = 'inline-block';
                }

                if (nextBtn) nextBtn.onclick = () => showSlide((current + 1) % 3);
                if (prevBtn) prevBtn.onclick = () => showSlide((current - 1 + 3) % 3);
                dots.forEach((dot, i) => dot.onclick = () => showSlide(i));
                setInterval(() => showSlide((current + 1) % 3), 8000);

                // ===== SLIDER BLOG =====
                const blogSlider = document.getElementById('blogSlider');
                const blogPrev = document.getElementById('blogPrev');
                const blogNext = document.getElementById('blogNext');
                const blogDots = document.querySelectorAll('.blog-dot');
                let blogCurrent = 0;

                function updateBlog() {
                    if (blogSlider) {
                        blogSlider.style.transform = 'translateX(-' + (blogCurrent * 100) + '%)';
                        blogDots.forEach((d, i) => d.classList.toggle('active', i === blogCurrent));
                    }
                }

                if (blogNext) blogNext.onclick = () => { blogCurrent = (blogCurrent + 1) % 2; updateBlog(); };
                if (blogPrev) blogPrev.onclick = () => { blogCurrent = (blogCurrent - 1 + 2) % 2; updateBlog(); };
                blogDots.forEach((dot, i) => dot.onclick = () => { blogCurrent = i; updateBlog(); });
            }
        })();
    </script>
</body>

</html>