<?php
session_start();
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
    header('Location: sign-in.php');
    exit;
}
$vehiculo_id = $_GET['id'] ?? null;
if (!$vehiculo_id) {
    header('Location: vehiculo_dashboard.php');
    exit;
}
?>
<form id="vehiculoDeleteForm" method="post" action="PHP/router.php/vehiculo/delete" style="display:none;">
    <input type="hidden" name="id" value="<?= htmlspecialchars($vehiculo_id) ?>">
</form>
<script>
    if (confirm('¿Seguro que deseas eliminar este vehículo?')) {
        document.getElementById('vehiculoDeleteForm').submit();
    } else {
        window.location.href = 'vehiculo_dashboard.php';
    }
</script>
exit;
