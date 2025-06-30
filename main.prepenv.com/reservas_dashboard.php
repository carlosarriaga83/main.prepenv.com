<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
    header('Location: sign-in.php');
    exit;
}
require_once './PHP/config.php';
include './partials/layouts/layoutTop.php';

// Set default timezone to Mexico City
date_default_timezone_set('America/Mexico_City');
$current_time = date('H:i');

$user_id = $_SESSION['USER']['ID'];
$reservas = [];
$error = '';
$success = '';

// Check DB connection
if (!isset($pdo) || !$pdo) {
    $error = "Error de conexión a la base de datos.";
} else {
    // Fetch reservations
    try {
        // registros_estacionamiento: id, id_usuario, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id, created_at
        $stmt = $pdo->prepare("SELECT id, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id, created_at FROM registros_estacionamiento WHERE id_usuario = ?");
        $stmt->execute([$user_id]);
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Error al obtener las reservas: " . $e->getMessage();
    }

    // Fetch vehicles for select options
    $vehiculos = [];
    try {
        $stmt = $pdo->prepare("SELECT id, matricula, alias FROM vehiculos_usuario WHERE id_usuario = ?");
        $stmt->execute([$user_id]);
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    // Fetch estacionamientos for select options
    $estacionamientos = [];
    try {
        $stmt = $pdo->query("SELECT id, nombre FROM estacionamientos");
        $estacionamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Reservas</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Reservas</li>
        </ul>
    </div>
</div>

<div class="mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservaModal">Agregar Reserva</button>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Add Reserva Modal -->
<div class="modal fade" id="addReservaModal" tabindex="-1" aria-labelledby="addReservaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addReservaModalLabel">Agregar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <div class="mb-3">
          <label for="hora_inicio" class="form-label">Hora Inicio</label>
          <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required value="<?= $current_time ?>">
        </div>
        <div class="mb-3">
          <label for="hora_fin" class="form-label">Hora Fin</label>
          <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
        </div>
        <div class="mb-3">
          <label for="id_estacionamiento" class="form-label">Estacionamiento</label>
          <select class="form-select" id="id_estacionamiento" name="id_estacionamiento" required>
            <option value="">Selecciona uno</option>
            <?php foreach ($estacionamientos as $est): ?>
                <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="id_vehiculo" class="form-label">Vehículo</label>
          <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
            <option value="">Selecciona uno</option>
            <?php foreach ($vehiculos as $veh): ?>
                <option value="<?= $veh['id'] ?>"><?= htmlspecialchars($veh['alias'] ?: $veh['matricula']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" name="add_reserva" value="1">Agregar</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Reserva Modal -->
<div class="modal fade" id="editReservaModal" tabindex="-1" aria-labelledby="editReservaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" id="editReservaForm">
      <div class="modal-header">
        <h5 class="modal-title" id="editReservaModalLabel">Editar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="mb-3">
          <label for="edit_fecha" class="form-label">Fecha</label>
          <input type="date" class="form-control" id="edit_fecha" name="edit_fecha" required>
        </div>
        <div class="mb-3">
          <label for="edit_hora_inicio" class="form-label">Hora Inicio</label>
          <input type="time" class="form-control" id="edit_hora_inicio" name="edit_hora_inicio" required>
        </div>
        <div class="mb-3">
          <label for="edit_hora_fin" class="form-label">Hora Fin</label>
          <input type="time" class="form-control" id="edit_hora_fin" name="edit_hora_fin" required>
        </div>
        <div class="mb-3">
          <label for="edit_id_estacionamiento" class="form-label">Estacionamiento</label>
          <select class="form-select" id="edit_id_estacionamiento" name="edit_id_estacionamiento" required>
            <option value="">Selecciona uno</option>
            <?php foreach ($estacionamientos as $est): ?>
                <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="edit_id_vehiculo" class="form-label">Vehículo</label>
          <select class="form-select" id="edit_id_vehiculo" name="edit_id_vehiculo" required>
            <option value="">Selecciona uno</option>
            <?php foreach ($vehiculos as $veh): ?>
                <option value="<?= $veh['id'] ?>"><?= htmlspecialchars($veh['alias'] ?: $veh['matricula']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" name="edit_reserva" value="1">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Reserva Modal -->
<div class="modal fade" id="deleteReservaModal" tabindex="-1" aria-labelledby="deleteReservaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" id="deleteReservaForm">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteReservaModalLabel">Eliminar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="delete_id" id="delete_id">
        <p>¿Seguro que deseas eliminar esta reserva?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger" name="delete_reserva" value="1">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<!-- Reservations Table -->
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha Entrada</th>
                <th>Hora Entrada</th>
                <th>Fecha Salida</th>
                <th>Hora Salida</th>
                <th>Estacionamiento</th>
                <th>Vehículo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reservas as $res): ?>
            <tr>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($res['fecha_hora_entrada']))) ?></td>
                <td><?= htmlspecialchars(date('H:i', strtotime($res['fecha_hora_entrada']))) ?></td>
                <td>
                    <?= $res['fecha_hora_salida'] ? htmlspecialchars(date('Y-m-d', strtotime($res['fecha_hora_salida']))) : '-' ?>
                </td>
                <td>
                    <?= $res['fecha_hora_salida'] ? htmlspecialchars(date('H:i', strtotime($res['fecha_hora_salida']))) : '-' ?>
                </td>
                <td>
                    <?php
                    $est = array_filter($estacionamientos, fn($e) => $e['id'] == $res['parking_id']);
                    echo htmlspecialchars($est ? reset($est)['nombre'] : '');
                    ?>
                </td>
                <td>
                    <?= htmlspecialchars($res['matricula']) ?>
                </td>
                <td>
                    <button 
                        class="btn p-0 text-warning me-2"
                        data-bs-toggle="modal"
                        data-bs-target="#editReservaModal"
                        data-id="<?= $res['id'] ?>"
                        data-fecha="<?= $res['fecha'] ?>"
                        data-hora_inicio="<?= $res['hora_inicio'] ?>"
                        data-hora_fin="<?= $res['hora_fin'] ?>"
                        data-id_estacionamiento="<?= $res['id_estacionamiento'] ?>"
                        data-id_vehiculo="<?= $res['id_vehiculo'] ?>"
                        title="Editar"
                    >
                        <iconify-icon icon="mdi:pencil" class="icon text-lg"></iconify-icon>
                    </button>
                    <button 
                        class="btn p-0 text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteReservaModal"
                        data-id="<?= $res['id'] ?>"
                        title="Eliminar"
                    >
                        <iconify-icon icon="mdi:trash-can" class="icon text-lg"></iconify-icon>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit modal
    var editModal = document.getElementById('editReservaModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('edit_id').value = button.getAttribute('data-id');
        document.getElementById('edit_fecha').value = button.getAttribute('data-fecha');
        document.getElementById('edit_hora_inicio').value = button.getAttribute('data-hora_inicio');
        document.getElementById('edit_hora_fin').value = button.getAttribute('data-hora_fin');
        document.getElementById('edit_id_estacionamiento').value = button.getAttribute('data-id_estacionamiento');
        document.getElementById('edit_id_vehiculo').value = button.getAttribute('data-id_vehiculo');
    });

    // Delete modal
    var deleteModal = document.getElementById('deleteReservaModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('delete_id').value = button.getAttribute('data-id');
    });
});
</script>

<?php
// Handle add reserva POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reserva'])) {
    $fecha = $_POST['fecha'] ?? '';
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $id_estacionamiento = $_POST['id_estacionamiento'] ?? '';
    $id_vehiculo = $_POST['id_vehiculo'] ?? '';
    if ($fecha && $hora_inicio && $hora_fin && $id_estacionamiento && $id_vehiculo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO registros_estacionamiento (id_usuario, fecha, hora_inicio, hora_fin, id_estacionamiento, id_vehiculo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $fecha, $hora_inicio, $hora_fin, $id_estacionamiento, $id_vehiculo]);
            echo "<script>location.href='reservas_dashboard.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al agregar la reserva.');</script>";
        }
    }
}

// Handle edit reserva POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_reserva'])) {
    $id = $_POST['edit_id'] ?? '';
    $fecha = $_POST['edit_fecha'] ?? '';
    $hora_inicio = $_POST['edit_hora_inicio'] ?? '';
    $hora_fin = $_POST['edit_hora_fin'] ?? '';
    $id_estacionamiento = $_POST['edit_id_estacionamiento'] ?? '';
    $id_vehiculo = $_POST['edit_id_vehiculo'] ?? '';
    if ($id && $fecha && $hora_inicio && $hora_fin && $id_estacionamiento && $id_vehiculo) {
        try {
            $stmt = $pdo->prepare("UPDATE registros_estacionamiento SET fecha = ?, hora_inicio = ?, hora_fin = ?, id_estacionamiento = ?, id_vehiculo = ? WHERE id = ? AND id_usuario = ?");
            $stmt->execute([$fecha, $hora_inicio, $hora_fin, $id_estacionamiento, $id_vehiculo, $id, $user_id]);
            echo "<script>location.href='reservas_dashboard.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al editar la reserva.');</script>";
        }
    }
}

// Handle delete reserva POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reserva'])) {
    $id = $_POST['delete_id'] ?? '';
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM registros_estacionamiento WHERE id = ? AND id_usuario = ?");
            $stmt->execute([$id, $user_id]);
            echo "<script>location.href='reservas_dashboard.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al eliminar la reserva.');</script>";
        }
    }
}
?>

<?php include './partials/layouts/layoutBottom.php'; ?>
