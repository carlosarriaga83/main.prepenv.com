<?php
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';
// No es necesario iniciar sesión aquí, ya que esta página es para usuarios que no pueden iniciar sesión.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - SOSMEX Cursos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="app-container">
        <div id="actual-reset-password-page" class="page active">
            <div class="form-container">
                <h2>Restablecer Contraseña</h2>
                <?php if (empty($token)): ?>
                    <p class="error-message">Token de restablecimiento no válido o faltante. Por favor, solicita un nuevo enlace de restablecimiento.</p>
                    <p><a href="index.html">Volver al inicio de sesión</a></p>
                <?php else: ?>
                    <form id="actual-reset-password-form">
                        <input type="hidden" id="reset-token-field" name="token" value="<?php echo $token; ?>">
                        <div class="form-group">
                            <label for="new-password">Nueva Contraseña (mínimo 6 caracteres):</label>
                            <input type="password" id="new-password" name="new_password" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirmar Nueva Contraseña:</label>
                            <input type="password" id="confirm-password" name="confirm_password" minlength="6" required>
                        </div>
                        <button type="submit">Restablecer Contraseña</button>
                        <p id="reset-form-error" class="error-message"></p>
                        <p id="reset-form-success" class="success-message" style="display:none;"></p>
                        <div id="reset-success-actions" style="display:none; margin-top:15px;"></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="script.js?c=<?php echo time(); // Cache busting para script.js ?>"></script>
</body>
</html>