<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
    header('Location: sign-in.php');
    exit;
}
include './partials/layouts/layoutTop.php';

// Use DB connection from config.php
require_once './PHP/config.php'; // $pdo must be defined in config.php

// Fetch vehicles from DB
$user_id = $_SESSION['USER']['ID']; // Use the same session structure as login.php
$vehicles = [];
try {
    $stmt = $pdo->prepare("SELECT id, matricula, alias FROM vehiculos_usuario WHERE id_usuario = ?");
    $stmt->execute([$user_id]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle error (optional: log or display a message)
}

// Handle add vehicle POST
$add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
    $matricula = trim($_POST['matricula'] ?? '');
    $alias = trim($_POST['alias'] ?? '');
    if ($matricula) {
        try {
            $stmt = $pdo->prepare("INSERT INTO vehiculos_usuario (id_usuario, matricula, alias) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $matricula, $alias]);
            header("Location: vehiculo_dashboard.php");
            exit;
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                $add_error = "Ya tienes un vehículo registrado con esa matrícula.";
            } else {
                $add_error = "Error al agregar el vehículo.";
            }
        }
    } else {
        $add_error = "La matrícula es obligatoria.";
    }
}
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Vehículos</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Vehículos</li>
        </ul>
    </div>
</div>

<!-- Vehicle Actions -->
<div class="mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehiculoModal">Agregar Vehículo</button>
</div>

<?php if ($add_error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($add_error) ?></div>
<?php endif; ?>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehiculoModal" tabindex="-1" aria-labelledby="addVehiculoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addVehiculoModalLabel">Agregar Vehículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="matricula" class="form-label">Matrícula</label>
          <input type="text" class="form-control" id="matricula" name="matricula" required>
        </div>
        <div class="mb-3">
          <label for="alias" class="form-label">Alias (opcional)</label>
          <input type="text" class="form-control" id="alias" name="alias">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" name="add_vehicle" value="1">Agregar</button>
      </div>
    </form>
  </div>
</div>

<div class="row">
    <?php if (empty($vehicles)): ?>
        <div class="col-12">
            <div class="alert alert-info">No tienes vehículos registrados.</div>
        </div>
    <?php else: ?>
        <?php foreach ($vehicles as $vehiculo): ?>
            <div class="col-md-4 mb-3">
                <div class="card p-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title"><?= htmlspecialchars($vehiculo['alias'] ?: $vehiculo['matricula']) ?></h5>
                            <p class="card-text">Matrícula: <?= htmlspecialchars($vehiculo['matricula']) ?></p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button 
                                class="btn p-0 text-warning me-2" 
                                title="Editar"
                                data-bs-toggle="modal"
                                data-bs-target="#editVehiculoModal"
                                data-id="<?= htmlspecialchars($vehiculo['id']) ?>"
                                data-matricula="<?= htmlspecialchars($vehiculo['matricula']) ?>"
                                data-alias="<?= htmlspecialchars($vehiculo['alias']) ?>"
                            >
                                <iconify-icon icon="mdi:pencil" class="icon text-lg"></iconify-icon>
                            </button>
                            <button 
                                class="btn p-0 text-danger" 
                                title="Eliminar"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteVehiculoModal"
                                data-id="<?= htmlspecialchars($vehiculo['id']) ?>"
                            >
                                <iconify-icon icon="mdi:trash-can" class="icon text-lg"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehiculoModal" tabindex="-1" aria-labelledby="editVehiculoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="PHP/router.php/vehiculo/edit" class="modal-content" id="editVehiculoForm">
      <div class="modal-header">
        <h5 class="modal-title" id="editVehiculoModalLabel">Editar Vehículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editVehiculoId">
        <div class="mb-3">
          <label for="editMatricula" class="form-label">Matrícula</label>
          <input type="text" class="form-control" id="editMatricula" name="matricula" required>
        </div>
        <div class="mb-3">
          <label for="editAlias" class="form-label">Alias (opcional)</label>
          <input type="text" class="form-control" id="editAlias" name="alias">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Vehicle Modal -->
<div class="modal fade" id="deleteVehiculoModal" tabindex="-1" aria-labelledby="deleteVehiculoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="PHP/router.php/vehiculo/delete" class="modal-content" id="deleteVehiculoForm">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteVehiculoModalLabel">Eliminar Vehículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="deleteVehiculoId">
        <p>¿Seguro que deseas eliminar este vehículo?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit modal
    var editModal = document.getElementById('editVehiculoModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var matricula = button.getAttribute('data-matricula');
        var alias = button.getAttribute('data-alias');
        document.getElementById('editVehiculoId').value = id;
        document.getElementById('editMatricula').value = matricula;
        document.getElementById('editAlias').value = alias;
    });

    // Delete modal
    var deleteModal = document.getElementById('deleteVehiculoModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        document.getElementById('deleteVehiculoId').value = id;
    });
});
</script>

<?php include './partials/layouts/layoutBottom.php' ?>
