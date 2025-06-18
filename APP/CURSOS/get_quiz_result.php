<?php
session_start();

header('Content-Type: application/json');

require_once 'sql_config.php'; // Ensure this path is correct

$response = ['success' => false, 'quizResult' => null, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit;
}

$userId = $_SESSION['user_id'];

if ($db_connection->connect_error) {
    $response['message'] = 'Error de conexión a la base de datos: ' . $db_connection->connect_error;
    echo json_encode($response);
    exit;
}

// Fetch the latest quiz result for the user
$stmt = $db_connection->prepare("SELECT score, total_questions, percentage_score, certificate_path, submission_date FROM quiz_results WHERE user_id = ? ORDER BY submission_date DESC LIMIT 1");
if (!$stmt) {
    $response['message'] = 'Error al preparar la consulta: ' . $db_connection->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$response['success'] = true;
$response['quizResult'] = $result->fetch_assoc(); // Will be null if no record found

$stmt->close();
$db_connection->close();
echo json_encode($response);
?>