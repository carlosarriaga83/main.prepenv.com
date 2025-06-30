<?php
session_start();
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

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
        // Use correct columns from vehiculos_usuario table: id, id_usuario, matricula, alias
        $stmt = $pdo->prepare("SELECT id, matricula, alias FROM vehiculos_usuario WHERE id_usuario = ?");
        $stmt->execute([$user_id]);
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    // Fetch estacionamientos for select options (only those registered by the user)
    $estacionamientos = [];
    try {
        $stmt = $pdo->prepare("
            SELECT pl.id, pl.name 
            FROM parking_lots pl
            INNER JOIN user_parking_access upa ON upa.parking_id = pl.id
            WHERE upa.user_id = ?
        ");
        $stmt->execute([$user_id]);
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

        <div class="row justify-content-center gy-4">

            <!-- Reservations Table -->

            <div class="col-lg-10">
                <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Reservaciones</h5>
                    <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addReservaModal">Agregar Reserva</button>
                </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0 col-12">
    <thead>
        <tr class="border-primary-600">
            <th>Reserva</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($reservas as $res): ?>
        <tr>
            <td>
                <div style="display: grid; grid-template-columns: 110px 1fr; gap: 0.25rem;">
                    <div><strong>Entrada:</strong></div>
                    <div><?= htmlspecialchars(date('Y-m-d', strtotime($res['fecha_hora_entrada']))) ?> <?= htmlspecialchars(date('H:i', strtotime($res['fecha_hora_entrada']))) ?></div>
                    <div><strong>Salida:</strong></div>
                    <div>
                        <?php if ($res['fecha_hora_salida']): ?>
                            <?= htmlspecialchars(date('Y-m-d', strtotime($res['fecha_hora_salida']))) ?> <?= htmlspecialchars(date('H:i', strtotime($res['fecha_hora_salida']))) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                    <div><strong>Est.:</strong></div>
                    <div>
                        <?php
                        $est = array_filter($estacionamientos, fn($e) => $e['id'] == $res['parking_id']);
                        echo htmlspecialchars($est ? reset($est)['name'] : '');
                        ?>
                    </div>
                    <div><strong>Vehículo:</strong></div>
                    <div><?= htmlspecialchars($res['matricula']) ?></div>
                </div>
            </td>
            <td>
                <?php
                $estado = $res['estado'];
                if ($estado === 'asignado') {
                    echo '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">' . htmlspecialchars($estado) . '</span>';
                } else {
                    echo '<span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">' . htmlspecialchars($estado) . '</span>';
                }
                ?>
            </td>
            <td>
                <div class="d-flex align-items-center gap-10 justify-content-center">
                    <button 
                        type="button"
                        class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                        data-bs-toggle="modal"
                        data-bs-target="#editReservaModal"
                        data-id="<?= $res['id'] ?>"
                        data-fecha="<?= isset($res['fecha']) ? htmlspecialchars($res['fecha']) : htmlspecialchars(date('Y-m-d', strtotime($res['fecha_hora_entrada']))) ?>"
                        data-hora_inicio="<?= isset($res['hora_inicio']) ? htmlspecialchars($res['hora_inicio']) : htmlspecialchars(date('H:i', strtotime($res['fecha_hora_entrada']))) ?>"
                        data-hora_fin="<?= isset($res['hora_fin']) ? htmlspecialchars($res['hora_fin']) : ($res['fecha_hora_salida'] ? htmlspecialchars(date('H:i', strtotime($res['fecha_hora_salida']))) : '') ?>"
                        data-id_estacionamiento="<?= isset($res['id_estacionamiento']) ? htmlspecialchars($res['id_estacionamiento']) : htmlspecialchars($res['parking_id']) ?>"
                        data-id_vehiculo="<?php
                            // Find the vehicle id for this reservation by matching matricula
                            $veh_id = '';
                            foreach ($vehiculos as $veh) {
                                if ($veh['matricula'] === $res['matricula']) {
                                    $veh_id = $veh['id'];
                                    break;
                                }
                            }
                            echo htmlspecialchars($veh_id);
                        ?>"
                        title="Editar"
                    >
                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                    </button>
                    <button 
                        type="button"
                        class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteReservaModal"
                        data-id="<?= $res['id'] ?>"
                        title="Eliminar"
                    >
                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                    </button>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
                        </div>
                    </div>
                </div>
            </div>

        </div>


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
          <div class="d-flex align-items-center gap-2">
            <input type="date" class="form-control" id="fecha" name="fecha" required>
            <button type="button" id="btn-hoy" class="btn btn-outline-primary btn-sm">Hoy</button>
            <button type="button" id="btn-manana" class="btn btn-outline-primary btn-sm">Mañana</button>
          </div>
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
                <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['name']) ?></option>
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
                <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="edit_id_vehiculo" class="form-label">Vehículo</label>
          <select class="form-select" id="edit_id_vehiculo" name="edit_id_vehiculo" required>
            <option value="">Selecciona uno</option>
            <?php foreach ($vehiculos as $veh): ?>
                <option value="<?= $veh['id'] ?>">
                    <?= htmlspecialchars(($veh['alias'] ? $veh['alias'] . ' - ' : '') . $veh['matricula']) ?>
                </option>
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





<script>
document.addEventListener('DOMContentLoaded', function () {
    // Prepare estacionamientos for JS
    var estacionamientos = <?php echo json_encode($estacionamientos); ?>;
    // Prepare vehiculos for JS
    var vehiculos = <?php echo json_encode($vehiculos); ?>;

    // Add modal: populate estacionamientos select on show
    var addModal = document.getElementById('addReservaModal');
    addModal.addEventListener('show.bs.modal', function () {
        var select = document.getElementById('id_estacionamiento');
        select.innerHTML = '<option value="">Selecciona uno</option>';
        estacionamientos.forEach(function(est) {
            var option = document.createElement('option');
            option.value = est.id;
            option.textContent = est.name;
            select.appendChild(option);
        });
        // Populate vehiculos select with alias and matricula
        var vehiculoSelect = document.getElementById('id_vehiculo');
        vehiculoSelect.innerHTML = '<option value="">Selecciona uno</option>';
        vehiculos.forEach(function(veh) {
            var option = document.createElement('option');
            option.value = veh.id;
            option.textContent = (veh.alias ? veh.alias + ' - ' : '') + veh.matricula;
            vehiculoSelect.appendChild(option);
        });
        // Set fecha to today by default
        var fechaInput = document.getElementById('fecha');
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var dd = String(today.getDate()).padStart(2, '0');
        fechaInput.value = yyyy + '-' + mm + '-' + dd;

        // Remove toggle from both buttons
        document.getElementById('btn-hoy').classList.remove('btn-primary');
        document.getElementById('btn-hoy').classList.add('btn-outline-primary');
        document.getElementById('btn-manana').classList.remove('btn-primary');
        document.getElementById('btn-manana').classList.add('btn-outline-primary');
        // Set toggle to "Hoy"
        document.getElementById('btn-hoy').classList.add('btn-primary');
        document.getElementById('btn-hoy').classList.remove('btn-outline-primary');
    });

    // Shortcut buttons for fecha in Add Reserva Modal
    var btnHoy = document.getElementById('btn-hoy');
    var btnManana = document.getElementById('btn-manana');
    var fechaInput = document.getElementById('fecha');
    if (btnHoy && btnManana && fechaInput) {
        btnHoy.addEventListener('click', function () {
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            fechaInput.value = yyyy + '-' + mm + '-' + dd;
            btnHoy.classList.add('btn-primary');
            btnHoy.classList.remove('btn-outline-primary');
            btnManana.classList.remove('btn-primary');
            btnManana.classList.add('btn-outline-primary');
        });
        btnManana.addEventListener('click', function () {
            var manana = new Date();
            manana.setDate(manana.getDate() + 1);
            var yyyy = manana.getFullYear();
            var mm = String(manana.getMonth() + 1).padStart(2, '0');
            var dd = String(manana.getDate()).padStart(2, '0');
            fechaInput.value = yyyy + '-' + mm + '-' + dd;
            btnManana.classList.add('btn-primary');
            btnManana.classList.remove('btn-outline-primary');
            btnHoy.classList.remove('btn-primary');
            btnHoy.classList.add('btn-outline-primary');
        });
        // Remove toggle if user manually changes date
        fechaInput.addEventListener('input', function () {
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            var todayStr = yyyy + '-' + mm + '-' + dd;

            var manana = new Date();
            manana.setDate(manana.getDate() + 1);
            var yyyyM = manana.getFullYear();
            var mmM = String(manana.getMonth() + 1).padStart(2, '0');
            var ddM = String(manana.getDate()).padStart(2, '0');
            var mananaStr = yyyyM + '-' + mmM + '-' + ddM;

            if (fechaInput.value === todayStr) {
                btnHoy.classList.add('btn-primary');
                btnHoy.classList.remove('btn-outline-primary');
                btnManana.classList.remove('btn-primary');
                btnManana.classList.add('btn-outline-primary');
            } else if (fechaInput.value === mananaStr) {
                btnManana.classList.add('btn-primary');
                btnManana.classList.remove('btn-outline-primary');
                btnHoy.classList.remove('btn-primary');
                btnHoy.classList.add('btn-outline-primary');
            } else {
                btnHoy.classList.remove('btn-primary');
                btnHoy.classList.add('btn-outline-primary');
                btnManana.classList.remove('btn-primary');
                btnManana.classList.add('btn-outline-primary');
            }
        });
    }

    // Edit modal
    var editModal = document.getElementById('editReservaModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        // Populate estacionamientos select
        var select = document.getElementById('edit_id_estacionamiento');
        select.innerHTML = '<option value="">Selecciona uno</option>';
        estacionamientos.forEach(function(est) {
            var option = document.createElement('option');
            option.value = est.id;
            option.textContent = est.name;
            select.appendChild(option);
        });

        // Populate vehiculos select with alias and matricula
        debugger;
        var vehiculoSelect = document.getElementById('edit_id_vehiculo');
        vehiculoSelect.innerHTML = '<option value="">Selecciona uno</option>';
        vehiculos.forEach(function(veh) {
            var option = document.createElement('option');
            option.value = veh.id;
            option.textContent = (veh.alias ? veh.alias + ' - ' : '') + veh.matricula;
            // Mark as selected if matches data-id_vehiculo
            if (
                button.getAttribute('data-id_vehiculo') &&
                String(veh.id) === String(button.getAttribute('data-id_vehiculo'))
            ) {
                option.selected = true;
            }
            vehiculoSelect.appendChild(option);
        });

        // Set all fields from data attributes
        document.getElementById('edit_id').value = button.getAttribute('data-id') || '';
        document.getElementById('edit_fecha').value = button.getAttribute('data-fecha') || '';
        document.getElementById('edit_hora_inicio').value = button.getAttribute('data-hora_inicio') || '';
        document.getElementById('edit_hora_fin').value = button.getAttribute('data-hora_fin') || '';
        select.value = button.getAttribute('data-id_estacionamiento') || '';
        // vehiculoSelect.value is set above via option.selected
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


