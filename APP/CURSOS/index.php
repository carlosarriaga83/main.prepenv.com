<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curso de Capacitación en Línea</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?a=<?php echo time(); // Cache busting  ?>">
    
</head>
<body>
    <div id="app-container">
        <div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
            <button id="theme-toggle-button">Tema Claro</button>
        </div>

        <!-- Login Page 1 -->
        <div id="login-page" class="page active">
            <div class="form-container">
                <h2>Iniciar Sesión</h2>
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-email">Correo Electrónico:</label>
                        <input type="email" id="login-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Contraseña:</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="login-licensekey">Clave de Licencia:</label>
                        <input type="text" id="login-licensekey" name="licenseKey" required>
                    </div>
                    <button type="submit">Ingresar</button>
                    <p id="login-error" class="error-message"></p>
                    <p class="toggle-form-link"><a href="#" id="show-forgot-password-link">¿Olvidaste tu contraseña?</a></p>
                    <p class="toggle-form-link">¿No tienes cuenta? <a href="#" id="show-register-link">Regístrate aquí</a></p>
                </form>
            </div>
        </div>

        <!-- Forgot Password Page -->
        <div id="forgot-password-page" class="page">
            <div class="form-container">
                <h2>Recuperar Contraseña</h2>
                <form id="forgot-password-form">
                    <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
                    <div class="form-group">
                        <label for="forgot-email">Correo Electrónico:</label>
                        <input type="email" id="forgot-email" name="email" required>
                    </div>
                    <button type="submit">Enviar Enlace de Recuperación</button>
                    <p id="forgot-password-error" class="error-message"></p>
                    <p id="forgot-password-success" class="success-message" style="display:none;"></p>
                    <p class="toggle-form-link"><a href="#" id="back-to-login-link">Volver a Iniciar Sesión</a></p>
                </form>
            </div>
        </div>

        <!-- Registration Page -->
        <div id="register-page" class="page">
            <div class="form-container">
                <h2>Crear Cuenta</h2>
                <form id="register-form">
                    <div class="form-group">
                        <label for="register-fullname">Nombre Completo:</label>
                        <input type="text" id="register-fullname" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label for="register-phone">Teléfono (10 dígitos):</label>
                        <input type="tel" id="register-phone" name="phone" pattern="\d{10}" title="Debe ser un número de 10 dígitos" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Correo Electrónico:</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Contraseña:</label>
                        <input type="password" id="register-password" name="password" minlength="6" required>
                    </div>
                    <button type="submit">Registrar</button>
                    <p id="register-error" class="error-message"></p>
                    <p id="register-success" class="success-message" style="display:none; color: green;"></p>
                    <p class="toggle-form-link">¿Ya tienes cuenta? <a href="#" id="show-login-link">Inicia sesión aquí</a></p>
                </form>
            </div>
        </div>

        <!-- Landing Page / Course Page -->
        <div id="course-page" class="page">
            <header>
                <h1>Título del Curso</h1>
                <button id="logout-button">Cerrar Sesión</button>
            </header>
            <main>
                <section id="course-description">
                    <h2 id="course-main-title">Acerca de Este Curso</h2>
                    <p>Bienvenido a este curso de capacitación integral. Aquí aprenderá sobre [Tema del Curso]. El curso consta de varios módulos de video. Mire todos los videos para desbloquear el cuestionario final.</p>
                </section>

                <section id="progress-view">
                    <h2>Tu Progreso</h2>
                    <div id="progress-bar-container">
                        <div id="progress-bar"></div>
                    </div>
                    <p id="progress-text">0/0 videos vistos</p>
                </section>

                <section id="video-section">
                    <div id="video-player-area">
                        <h3 id="current-video-title">Selecciona un video para comenzar</h3>
                        <video id="course-video" controls width="100%"></video>
                        <p id="video-error" class="error-message"></p>
                    </div>
                    <div id="video-list-container">
                        <h2>Videos del Curso</h2>
                        <ul id="video-list">
                            <!-- Video items will be populated by JavaScript -->
                        </ul>
                        <button id="proceed-to-quiz-button" type="button" style="display: none; margin-top: 10px;">Proceder al Cuestionario</button>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- Quiz Section -->
    <section id="quiz-section" class="quiz-container page">
        <h2>Cuestionario del Curso</h2>
        <p id="quiz-locked-message" style="color: #777; font-style: italic;">Debes completar todos los videos para acceder al cuestionario.</p>
        <div id="quiz-content" style="display:none;"> <!-- Wrapper for active quiz, shown by JS -->
            <form id="quiz-form">
                <!-- Current question content (label, inputs) will be populated here by JavaScript -->
            </form>
            <div id="quiz-navigation" style="margin-top: 15px; text-align: center;">
                <button id="prev-question-button" type="button">Anterior</button>
                <span id="question-counter" style="margin: 0 10px;"></span> <!-- e.g., Pregunta 1 / 10 -->
                <button id="next-question-button" type="button">Siguiente</button>
            </div>
            <button id="submit-quiz-button" type="button" style="display: none; margin-top: 20px;">Enviar Cuestionario</button>
            <div id="quiz-results" style="margin-top: 15px;"></div>
        </div>
    </section>

    <script src="script.js?c=<?php echo time(); // Cache busting para script.js ?>"></script>
</body>
</html>