<?php
session_start();
require_once 'sql_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

$video_internal_id = $input['video_internal_id'] ?? null;
$status = $input['status'] ?? null; // 'in_progress' or 'watched'
$last_watched_seconds = $input['last_watched_seconds'] ?? 0;

if ($video_internal_id === null || $status === null) {
    $response['message'] = 'Datos de progreso incompletos.';
    echo json_encode($response);
    exit;
}

if (!in_array($status, ['in_progress', 'watched'])) {
    $response['message'] = 'Estado de video inválido.';
    echo json_encode($response);
    exit;
}

$stmt = $db_connection->prepare("INSERT INTO user_video_progress (user_id, video_internal_id, status, last_watched_seconds) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), last_watched_seconds = GREATEST(last_watched_seconds, VALUES(last_watched_seconds))");

if ($stmt) {
    $stmt->bind_param("iisd", $user_id, $video_internal_id, $status, $last_watched_seconds);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Progreso actualizado.';
    } else {
        $response['message'] = 'Error al actualizar el progreso: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['message'] = 'Error al preparar la actualización de progreso: ' . $db_connection->error;
}

echo json_encode($response);
mysqli_close($db_connection);
?>