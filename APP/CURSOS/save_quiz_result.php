<?php
// Temporal: Habilitar reporte de todos los errores para depuración
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();

header('Content-Type: application/json');

// Usar la misma configuración de BD que login.php
require_once 'sql_config.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$userId = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
    exit;
}

$score = isset($data['score']) ? (int)$data['score'] : null;
$totalQuestions = isset($data['total_questions']) ? (int)$data['total_questions'] : null;
$percentage = isset($data['percentage']) ? (float)$data['percentage'] : null;
$certificatePath = isset($data['certificate_path']) ? $data['certificate_path'] : null; // Nueva línea

if ($score === null || $totalQuestions === null || $percentage === null) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos: score, total_questions y percentage son requeridos.']);
    exit;
}


if ($db_connection->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $db_connection->connect_error]);
    exit;
}

$sql = "INSERT INTO quiz_results (user_id, score, total_questions, percentage_score, certificate_path, submission_date) 
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
        score = VALUES(score), 
        total_questions = VALUES(total_questions), 
        percentage_score = VALUES(percentage_score), 
        certificate_path = VALUES(certificate_path),
        submission_date = NOW()";

$stmt = $db_connection->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $db_connection->error]);
    exit;
}

$stmt->bind_param("iiids", $userId, $score, $totalQuestions, $percentage, $certificatePath);

try {
    $executionSuccess = $stmt->execute();

    if ($executionSuccess) {
        $affected_rows = $stmt->affected_rows;
        $message = '';

        // Para INSERT ... ON DUPLICATE KEY UPDATE:
        // affected_rows = 1: nueva fila insertada.
        // affected_rows = 2: fila existente actualizada (valores nuevos diferentes de los antiguos).
        // affected_rows = 0: fila existente encontrada, pero los valores nuevos eran iguales a los antiguos (sin actualización real).
        if ($affected_rows == 1) {
            $message = 'Resultado del cuestionario guardado exitosamente (nuevo).';
        } elseif ($affected_rows >= 2) { // Cubre 2 para actualización
            $message = 'Resultado del cuestionario actualizado exitosamente.';
        } elseif ($affected_rows == 0) {
            $message = 'El resultado del cuestionario no cambió (ya existía con los mismos valores).';
        } else {
            // Este caso no debería ser < 0 si execute() fue true.
            $message = 'Operación completada, filas afectadas: ' . $affected_rows;
        }
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        // Este bloque se alcanzaría si execute() devuelve false Y mysqli_report NO está configurado para lanzar excepciones para este error.
        // Dada la "Uncaught mysqli_sql_exception", este camino es menos probable para el error reportado.
        error_log("Error (execute devolvió false) al guardar/actualizar resultado para user_id $userId: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar la operación en la base de datos.']);
    }
} catch (mysqli_sql_exception $e) {
    error_log("mysqli_sql_exception para user_id $userId: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
    $responseMessage = 'Error de base de datos al guardar el resultado.';
    if ($e->getCode() == 1062) { // Código de error de MySQL para entrada duplicada
        $responseMessage = 'Error: Conflicto de entrada duplicada. Aunque se intentó actualizar, la operación falló. Detalles: ' . $e->getMessage();
    } else {
        $responseMessage .= ' Detalles: ' . $e->getMessage();
    }
    echo json_encode(['success' => false, 'message' => $responseMessage]);
}

$stmt->close();
$db_connection->close();
?>