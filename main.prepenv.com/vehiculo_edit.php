<?php
session_start();
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
    header('Location: sign-in.php');
    exit;
}
require_once './PHP/api.php';

$user_id = $_SESSION['USER']['ID'];
$vehiculo_id = $_GET['id'] ?? null;
$error = '';

if (!$vehiculo_id) {
    header('Location: vehiculo_dashboard.php');
    exit;
}

$vehiculo = getVehicle($vehiculo_id, $user_id);
if (!$vehiculo) {
    header('Location: vehiculo_dashboard.php');
    exit;
}

if (isset($_GET['error'])) {
    $error = "Error al actualizar el vehículo. Verifica que la matrícula no esté repetida.";
}

include './partials/layouts/layoutTop.php';
?>

<div class="container mt-5">
    <h2>Editar Vehículo</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="PHP/router.php/vehiculo/edit">
        <input type="hidden" name="id" value="<?= htmlspecialchars($vehiculo['id']) ?>">
        <div class="mb-3">
            <label for="matricula" class="form-label">Matrícula</label>
            <input type="text" class="form-control" id="matricula" name="matricula" value="<?= htmlspecialchars($vehiculo['matricula']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="alias" class="form-label">Alias (opcional)</label>
            <input type="text" class="form-control" id="alias" name="alias" value="<?= htmlspecialchars($vehiculo['alias']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="vehiculo_dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>
        $edit_error = "La matrícula es obligatoria.";
    }
}
?>

<!-- HTML Form -->
<?php include './partials/layouts/layoutTop.php'; ?>
<div class="container mt-5">
    <h2>Editar Vehículo</h2>
    <?php if ($edit_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($edit_error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="matricula" class="form-label">Matrícula</label>
            <input type="text" class="form-control" id="matricula" name="matricula" value="<?= htmlspecialchars($vehiculo['matricula']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="alias" class="form-label">Alias (opcional)</label>
            <input type="text" class="form-control" id="alias" name="alias" value="<?= htmlspecialchars($vehiculo['alias']) ?>">
        </div>
        <button type="submit" class="btn btn-primary" name="edit_vehicle" value="1">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?php include './partials/layouts/layoutBottom.php'; ?>
