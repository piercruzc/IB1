<section id="contacto" class="cta-modern-with-form">
    <div class="container">
        <div class="cta-content-with-form">
            <div class="cta-text">
                <h2>¿Listo para hacer crecer tu patrimonio?</h2>
                <p>El futuro financiero de tu familia empieza hoy. No lo pospongas, decide con claridad y confianza.
                </p>
                <div class="cta-features">
                    <div class="cta-feature">
                        <span class="feature-check">✓</span>
                        <span>Actuamos con ética, siempre a favor de tu patrimonio.</span>
                    </div>
                    <div class="cta-feature">
                        <span class="feature-check">✓</span>
                        <span>Información clara para decisiones seguras.</span>
                    </div>
                    <div class="cta-feature">
                        <span class="feature-check">✓</span>
                        <span>Confianza construida con experiencia y resultados.</span>
                    </div>
                </div>
            </div>

            <!-- Formulario Integrado -->
            <div class="cta-form-container">
                <div class="form-header">
                    <h3>Solicita tu asesoría gratuita</h3>
                    <p>Completa el formulario y un experto te contactará</p>
                </div>

                <form class="modern-form" method="post" action="process_contact.php">
                    <!-- Hidden input for redirect URL -->
                    <input type="hidden" name="redirect_url"
                        value="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

                    <div class="form-row">
                        <div class="form-group-modern">
                            <label for="nombre">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="form-group-modern">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="Ej: +51 999 888 777" required>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu-email@ejemplo.com" required>
                    </div>

                    <div class="form-group-modern">
                        <label for="consulta">Motivo de consulta</label>
                        <select id="consulta" name="consulta" required>
                            <option value="">Selecciona una opción</option>
                            <option value="asesoria-finanzas-personales">Asesoría en Finanzas Personales</option>
                            <option value="estructuracion-y-reestructuracion-de-deuda">Estructuración y
                                Reestructuración de Deuda</option>
                            <option value="asesoria-inversion-ahorro">Asesoría en Inversión y Ahorro</option>
                            <option value="gestion-patrimonio">Gestión de Patrimonio</option>
                            <option value="curso-finanzas-personales-inteligentes">Curso: Finanzas Personales
                                Inteligentes</option>
                            <option value="curso-inversiones-activos-digitales">Curso: Inversiones en Activos
                                Digitales</option>
                            <option value="curso-inversiones-bolsa-de-valores">Curso: Inversiones en Bolsa de
                                Valores</option>
                            <option value="nivel-1">Plan Nivel 1 (S/ 500 - S/ 10,000)</option>
                            <option value="nivel-2">Plan Nivel 2 (S/ 10,001 - S/ 20,000)</option>
                            <option value="nivel-3">Plan Nivel 3 (S/ 20,001 a más)</option>
                        </select>
                    </div>

                    <div class="form-group-modern">
                        <label for="mensaje">Cuéntanos sobre tus objetivos</label>
                        <textarea id="mensaje" name="mensaje" rows="4"
                            placeholder="Describe tu situación actual y objetivos financieros..."></textarea>
                    </div>

                    <!-- reCAPTCHA Widget -->
                    <div class="form-group-modern">
                        <div class="g-recaptcha"
                            data-sitekey="<?php echo getenv('RECAPTCHA_SITE_KEY') ?: 'YOUR_SITE_KEY_HERE'; ?>"></div>
                    </div>

                    <div class="form-privacy">
                        <label class="checkbox-container">
                            <input type="checkbox" name="privacidad" required>
                            <span class="checkmark"></span>
                            Acepto la <a href="privacidad.html">política de privacidad</a> y el tratamiento de mis
                            datos
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        <span>Solicitar asesoría gratuita</span>
                        <span class="btn-arrow">→</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>