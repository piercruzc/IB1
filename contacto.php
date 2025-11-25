<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mensaje Enviado - IBM FINTECH S.A.C.</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="icon" type="image/png" href="img/favicon.png">
  <style>
    /* Estilos específicos para la página de confirmación */
    .success-message-section {
      min-height: calc(100vh - 400px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 100px 0 80px 0;
      background-color: #f8fafc;
    }

    .success-container {
      max-width: 700px;
      margin: 0 auto;
      text-align: center;
      background: white;
      padding: 60px 40px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(34, 73, 154, 0.15);
    }

    .success-icon {
      width: 100px;
      height: 100px;
      margin: 0 auto 30px;
      background: linear-gradient(135deg, #22499a 0%, #165ea9 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: successPulse 1.5s ease-in-out infinite;
    }

    .success-icon svg {
      width: 50px;
      height: 50px;
      stroke: white;
      stroke-width: 3;
      fill: none;
      animation: checkDraw 0.8s ease-out;
    }

    @keyframes successPulse {
      0%, 100% {
        box-shadow: 0 0 0 0 rgba(34, 73, 154, 0.4);
      }
      50% {
        box-shadow: 0 0 0 20px rgba(34, 73, 154, 0);
      }
    }

    @keyframes checkDraw {
      0% {
        stroke-dashoffset: 100;
        stroke-dasharray: 100;
      }
      100% {
        stroke-dashoffset: 0;
        stroke-dasharray: 100;
      }
    }

    .success-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #22499a;
      margin-bottom: 20px;
      line-height: 1.3;
    }

    .success-description {
      font-size: 1.1rem;
      color: #666;
      line-height: 1.7;
      margin-bottom: 30px;
    }

    .redirect-info {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin: 30px 0;
      padding: 20px;
      background: rgba(34, 73, 154, 0.05);
      border-radius: 12px;
      font-size: 1rem;
      color: #333;
    }

    .countdown {
      font-weight: 700;
      color: #22499a;
      font-size: 1.3rem;
      min-width: 30px;
      display: inline-block;
    }

    .redirect-link {
      display: inline-block;
      background: linear-gradient(135deg, #22499a 0%, #165ea9 100%);
      color: white;
      padding: 15px 35px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .redirect-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(34, 73, 154, 0.4);
      text-decoration: none;
    }

    .success-features {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 40px;
      text-align: left;
    }

    .success-feature {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 1rem;
      color: #555;
    }

    .feature-check-success {
      background: #22c55e;
      color: white;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-weight: 700;
      flex-shrink: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .success-message-section {
        padding: 80px 0 60px 0;
        min-height: calc(100vh - 350px);
      }

      .success-container {
        padding: 40px 25px;
        margin: 0 15px;
      }

      .success-icon {
        width: 80px;
        height: 80px;
      }

      .success-icon svg {
        width: 40px;
        height: 40px;
      }

      .success-title {
        font-size: 1.8rem;
      }

      .success-description {
        font-size: 1rem;
      }

      .redirect-info {
        flex-direction: column;
        gap: 8px;
        padding: 15px;
        font-size: 0.95rem;
      }

      .countdown {
        font-size: 1.1rem;
      }

      .redirect-link {
        padding: 12px 30px;
        font-size: 0.95rem;
      }

      .success-features {
        gap: 12px;
      }

      .success-feature {
        font-size: 0.95rem;
      }
    }

    @media (max-width: 480px) {
      .success-container {
        padding: 30px 20px;
      }

      .success-title {
        font-size: 1.5rem;
      }

      .success-description {
        font-size: 0.95rem;
      }
    }
  </style>
</head>

<body>
<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Sección de Mensaje de Éxito -->
<section class="success-message-section">
  <div class="container">
    <div class="success-container">
      <!-- Ícono de éxito -->
      <div class="success-icon">
        <svg viewBox="0 0 24 24">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>

      <!-- Título -->
      <h1 class="success-title">¡Mensaje enviado con éxito!</h1>

      <!-- Descripción -->
      <p class="success-description">
        Su mensaje ha sido enviado correctamente. Nos pondremos en contacto con usted en la brevedad posible
        para brindarle la asesoría que necesita.
      </p>

      <!-- Información de redirección -->
      <div class="redirect-info">
        <span>Será redireccionado en</span>
        <span class="countdown" id="countdown">5</span>
        <span>segundos</span>
      </div>

      <!-- Botón de redirección manual -->
      <a href="index.html" class="redirect-link">O haga clic aquí para volver al inicio</a>

      <!-- Features adicionales -->
      <div class="success-features">
        <div class="success-feature">
          <span class="feature-check-success">✓</span>
          <span>Un asesor experto revisará su consulta</span>
        </div>
        <div class="success-feature">
          <span class="feature-check-success">✓</span>
          <span>Recibirá respuesta dentro de las próximas 24 horas</span>
        </div>
        <div class="success-feature">
          <span class="feature-check-success">✓</span>
          <span>Sin compromisos, evaluación completamente gratuita</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Scripts -->
<script>
  (function() {
    'use strict';

    // Inicialización cuando el DOM esté listo
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      init();
    }

    function init() {
      // ===== MENÚ MÓVIL =====
      const hamburger = document.querySelector('.hamburger-blue');
      const navMenu = document.querySelector('.nav-menu-blue');

      if (hamburger && navMenu) {
        hamburger.onclick = function() {
          this.classList.toggle('active');
          navMenu.classList.toggle('active');
        };

        const links = navMenu.querySelectorAll('a');
        links.forEach(function(link) {
          link.onclick = function() {
            if (window.innerWidth <= 768) {
              hamburger.classList.remove('active');
              navMenu.classList.remove('active');
            }
          };
        });

        document.onclick = function(e) {
          if (window.innerWidth <= 768 && !e.target.closest('.navbar-blue')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
          }
        };
      }

      // ===== CONTADOR DE REDIRECCIÓN =====
      let seconds = 5;
      const countdownElement = document.getElementById('countdown');

      const countdownInterval = setInterval(function() {
        seconds--;
        if (countdownElement) {
          countdownElement.textContent = seconds;
        }

        if (seconds <= 0) {
          clearInterval(countdownInterval);
          window.location.href = 'index.html';
        }
      }, 1000);
    }
  })();
</script>
</body>

</html>
