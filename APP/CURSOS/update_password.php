<?php
// No es necesario session_start() aquí a menos que planees iniciar sesión al usuario inmediatamente después.
require_once 'sql_config.php'; // Conexión a la BD

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? null;
    $newPassword = $input['new_password'] ?? null;
    $confirmPassword = $input['confirm_password'] ?? null;

    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        $response['message'] = 'Todos los campos son requeridos.';
        echo json_encode($response);
        exit;
    }

    if (strlen($newPassword) < 6) {
        $response['message'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
        echo json_encode($response);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $response['message'] = 'Las contraseñas no coinciden.';
        echo json_encode($response);
        exit;
    }

    $stmt_check_token = $db_connection->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ?");
    if (!$stmt_check_token) {
        $response['message'] = 'Error al preparar la consulta del token: ' . $db_connection->error;
        error_log("DB Prepare Error (check_token) in update_password: " . $db_connection->error);
        echo json_encode($response);
        exit;
    }
    $stmt_check_token->bind_param("s", $token);
    $stmt_check_token->execute();
    $result_token = $stmt_check_token->get_result();

    if ($user = $result_token->fetch_assoc()) {
        $userId = $user['id'];
        $expiryDateStr = $user['reset_token_expiry'];

        if (empty($expiryDateStr)) {
            $response['message'] = 'Token inválido o ya utilizado (sin fecha de expiración).';
            $stmt_check_token->close();
            $db_connection->close();
            echo json_encode($response);
            exit;
        }
        
        try {
            $expiryDate = new DateTime($expiryDateStr);
            $currentDate = new DateTime();

            if ($expiryDate < $currentDate) {
                $response['message'] = 'El token de restablecimiento ha expirado.';
                // Invalidar el token expirado
                $stmt_invalidate_expired = $db_connection->prepare("UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = ? AND reset_token = ?");
                if ($stmt_invalidate_expired) {
                    $stmt_invalidate_expired->bind_param("is", $userId, $token);
                    $stmt_invalidate_expired->execute();
                    $stmt_invalidate_expired->close();
                }
                $stmt_check_token->close();
                $db_connection->close();
                echo json_encode($response);
                exit;
            }
        } catch (Exception $e) {
            $response['message'] = 'Error al procesar la fecha de expiración del token.';
            error_log("DateTime Exception in update_password for token $token: " . $e->getMessage());
            $stmt_check_token->close();
            $db_connection->close();
            echo json_encode($response);
            exit;
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt_update_password = $db_connection->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ? AND reset_token = ?");
        
        if (!$stmt_update_password) {
            $response['message'] = 'Error al preparar la actualización de contraseña: ' . $db_connection->error;
            error_log("DB Prepare Error (update_password) in update_password: " . $db_connection->error);
        } else {
            $stmt_update_password->bind_param("sis", $newPasswordHash, $userId, $token);
            if ($stmt_update_password->execute() && $stmt_update_password->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.';
            } else {
                $response['message'] = 'No se pudo actualizar la contraseña. El token podría ser inválido, haber expirado o ya fue utilizado. Error: ' . $stmt_update_password->error;
                error_log("DB Execute/AffectedRows Error (update_password) for user_id $userId: " . $stmt_update_password->error);
            }
            $stmt_update_password->close();
        }
    } else {
        $response['message'] = 'Token de restablecimiento no válido o no encontrado.';
    }
    $stmt_check_token->close();
} else {
    $response['message'] = 'Método de solicitud no válido.';
}

if (isset($db_connection) && $db_connection instanceof mysqli && $db_connection->thread_id) {
    $db_connection->close();
}
echo json_encode($response);
?>