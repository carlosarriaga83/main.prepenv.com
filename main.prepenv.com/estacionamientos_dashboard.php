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

$error = '';
$success = '';

// Fetch parkings
$parkings = [];
try {
    $stmt = $pdo->query("SELECT id, name, address, access_code FROM parking_lots");
    $parkings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch latest estado for each parking lot
    $estados = [];
    $stmtEstado = $pdo->query("
        SELECT parking_id, estado
        FROM registros_estacionamiento
        WHERE id IN (
            SELECT MAX(id) FROM registros_estacionamiento GROUP BY parking_id
        )
    ");
    foreach ($stmtEstado->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $estados[$row['parking_id']] = $row['estado'];
    }
} catch (Exception $e) {
    $error = "Error al obtener los estacionamientos: " . $e->getMessage();
}

// Handle add parking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_parking'])) {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $access_code = trim($_POST['access_code'] ?? '');
    if ($name && $access_code) {
        try {
            $stmt = $pdo->prepare("INSERT INTO parking_lots (name, address, access_code) VALUES (?, ?, ?)");
            $stmt->execute([$name, $address, $access_code]);
            header("Location: estacionamientos_dashboard.php");
            exit;
        } catch (Exception $e) {
            $error = "Error al agregar el estacionamiento. El código de acceso debe ser único.";
        }
    } else {
        $error = "Nombre y código de acceso son obligatorios.";
    }
}

// Handle edit parking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_parking'])) {
    $id = intval($_POST['edit_id'] ?? 0);
    $name = trim($_POST['edit_name'] ?? '');
    $address = trim($_POST['edit_address'] ?? '');
    $access_code = trim($_POST['edit_access_code'] ?? '');
    if ($id && $name && $access_code) {
        try {
            $stmt = $pdo->prepare("UPDATE parking_lots SET name = ?, address = ?, access_code = ? WHERE id = ?");
            $stmt->execute([$name, $address, $access_code, $id]);
            header("Location: estacionamientos_dashboard.php");
            exit;
        } catch (Exception $e) {
            $error = "Error al editar el estacionamiento. El código de acceso debe ser único.";
        }
    } else {
        $error = "Nombre y código de acceso son obligatorios.";
    }
}

// Handle delete parking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_parking'])) {
    $id = intval($_POST['delete_id'] ?? 0);
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM parking_lots WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: estacionamientos_dashboard.php");
            exit;
        } catch (Exception $e) {
            $error = "Error al eliminar el estacionamiento.";
        }
    }
}
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Estacionamientos</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Estacionamientos</li>
        </ul>
    </div>

    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Estacionamientos</h5>
                    <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addParkingModal">Agregar Estacionamiento</button>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="ttable bordered-table mb-0 col-12">
                            <thead>
                                <tr class="border-primary-600">
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Código de Acceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($parkings as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= htmlspecialchars($p['address']) ?></td>
                                    <td><?= htmlspecialchars($p['access_code']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button 
                                                type="button"
                                                class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editParkingModal"
                                                data-id="<?= $p['id'] ?>"
                                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                                data-address="<?= htmlspecialchars($p['address']) ?>"
                                                data-access_code="<?= htmlspecialchars($p['access_code']) ?>"
                                                title="Editar"
                                            >
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                            <button 
                                                type="button"
                                                class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteParkingModal"
                                                data-id="<?= $p['id'] ?>"
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

<!-- Add Parking Modal -->
<div class="modal fade" id="addParkingModal" tabindex="-1" aria-labelledby="addParkingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addParkingModalLabel">Agregar Estacionamiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="name" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Dirección</label>
          <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="mb-3">
          <label for="access_code" class="form-label">Código de Acceso</label>
          <input type="text" class="form-control" id="access_code" name="access_code" maxlength="6" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" name="add_parking" value="1">Agregar</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Parking Modal -->
<div class="modal fade" id="editParkingModal" tabindex="-1" aria-labelledby="editParkingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" id="editParkingForm">
      <div class="modal-header">
        <h5 class="modal-title" id="editParkingModalLabel">Editar Estacionamiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="mb-3">
          <label for="edit_name" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="edit_name" name="edit_name" required>
        </div>
        <div class="mb-3">
          <label for="edit_address" class="form-label">Dirección</label>
          <input type="text" class="form-control" id="edit_address" name="edit_address">
        </div>
        <div class="mb-3">
          <label for="edit_access_code" class="form-label">Código de Acceso</label>
          <input type="text" class="form-control" id="edit_access_code" name="edit_access_code" maxlength="6" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" name="edit_parking" value="1">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Parking Modal -->
<div class="modal fade" id="deleteParkingModal" tabindex="-1" aria-labelledby="deleteParkingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content" id="deleteParkingForm">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteParkingModalLabel">Eliminar Estacionamiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="delete_id" id="delete_id">
        <p>¿Seguro que deseas eliminar este estacionamiento?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger" name="delete_parking" value="1">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit modal
    var editModal = document.getElementById('editParkingModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('edit_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_address').value = button.getAttribute('data-address');
        document.getElementById('edit_access_code').value = button.getAttribute('data-access_code');
    });

    // Delete modal
    var deleteModal = document.getElementById('deleteParkingModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('delete_id').value = button.getAttribute('data-id');
    });
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
