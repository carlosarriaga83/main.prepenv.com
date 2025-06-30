<?php
session_start();
require_once './PHP/config.php';

// Check DB connection
if (!isset($pdo) || !$pdo) {
    die('Error de conexión a la base de datos.');
}

if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
    header('Location: sign-in.php');
    exit;
}

$user_id = $_SESSION['USER']['ID'];
$user = null;
$error = '';
$success = '';

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT id, telefono, email FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $error = "Usuario no encontrado.";
    }
} catch (Exception $e) {
    $error = "Error al obtener los datos del usuario: " . $e->getMessage();
}

// Handle edit user POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($telefono) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET telefono = ?, email = ? WHERE id = ?");
            $stmt->execute([$telefono, $email ?: null, $user_id]);
            $success = "Datos actualizados correctamente.";
            $user['telefono'] = $telefono;
            $user['email'] = $email;
        } catch (Exception $e) {
            $error = "Error al actualizar los datos: " . $e->getMessage();
        }
    } else {
        $error = "El teléfono es obligatorio.";
    }
}

// Handle delete account POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        session_unset();
        session_destroy();
        header('Location: sign-in.php');
        exit;
    } catch (Exception $e) {
        $error = "Error al eliminar la cuenta: " . $e->getMessage();
    }
}
?>

<?php include './partials/layouts/layoutTop.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card p-4 shadow radius-16">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-4">
                        <img src="assets/images/user.png" alt="Avatar" class="rounded-circle" style="width:80px;height:80px;">
                    </div>
                    <div>
                        <h4 class="mb-1">Mi Perfil</h4>
                        <div class="text-secondary-light">ID: <?= htmlspecialchars($user_id) ?></div>
                    </div>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if ($user): ?>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico (opcional)</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" name="edit_user" value="1">Guardar Cambios</button>
                        <button type="submit" class="btn btn-danger" name="delete_account" value="1" onclick="return confirm('¿Seguro que deseas eliminar tu cuenta?');">Eliminar Cuenta</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>
