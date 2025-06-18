<?php
session_start();
require_once 'sql_config.php'; // Conexión a la BD
require ('/home/u124132715/domains/prepenv.com/public_html/SOSMEX/vendor/autoload.php'); // Composer Autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? null;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Por favor, ingrese un correo electrónico válido.';
        echo json_encode($response);
        exit;
    }

    // Verificar si el email existe
    $stmt_check_email = $db_connection->prepare("SELECT id, full_name FROM users WHERE email = ?");
    if (!$stmt_check_email) {
        $response['message'] = 'Error al preparar la consulta: ' . $db_connection->error;
        error_log("DB Prepare Error (check_email) in forgot_password: " . $db_connection->error);
        echo json_encode($response);
        exit;
    }
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_user = $stmt_check_email->get_result();

    if ($user = $result_user->fetch_assoc()) {
        $userId = $user['id'];
        $userName = $user['full_name'];

        // Generar token y fecha de expiración (ej. 1 hora)
        $token = bin2hex(random_bytes(32));
        $expiryDate = date('Y-m-d H:i:s', time() + 3600); // 1 hora desde ahora

        // Guardar token en la base de datos
        $stmt_update_token = $db_connection->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        if (!$stmt_update_token) {
            $response['message'] = 'Error al preparar la actualización del token: ' . $db_connection->error;
            error_log("DB Prepare Error (update_token) in forgot_password: " . $db_connection->error);
            $stmt_check_email->close();
            echo json_encode($response);
            exit;
        }
        $stmt_update_token->bind_param("ssi", $token, $expiryDate, $userId);
        
        if ($stmt_update_token->execute()) {
            // Enviar correo electrónico
            $resetLink = "https://sosmex.prepenv.com/APP/CURSOS/reset_password_form.php?token=" . $token; // Ajusta esta URL a tu dominio y ruta correctos
            
            $mail = new PHPMailer(true);
            try {
                // Configuración SMTP (igual que en create_and_save_certificate.php)
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'constancias@sosmexicoriesgo.com';
                $mail->Password   = 'Pluma123.'; // Considera usar variables de entorno para credenciales
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';
                $mail->XMailer    = ' ';

                $mail->setFrom('constancias@sosmexicoriesgo.com', 'SOSMEX Cursos Soporte');
                $mail->addAddress($email, $userName);

                $mail->isHTML(true);
                $mail->Subject = 'Restablecimiento de Contraseña - SOSMEX Cursos';
                $mail->Body    = "Estimado/a " . htmlspecialchars($userName) . ",<br><br>" .
                                 "Hemos recibido una solicitud para restablecer tu contraseña. <br>" .
                                 "Haz clic en el siguiente enlace para continuar: <a href='" . $resetLink . "'>" . $resetLink . "</a><br><br>" .
                                 "Si no solicitaste esto, puedes ignorar este correo.<br><br>" .
                                 "Saludos,<br>El equipo de SOSMEX Cursos";
                $mail->AltBody = "Estimado/a " . $userName . ",\n\n" .
                                 "Hemos recibido una solicitud para restablecer tu contraseña.\n" .
                                 "Copia y pega el siguiente enlace en tu navegador para continuar: " . $resetLink . "\n\n" .
                                 "Si no solicitaste esto, puedes ignorar este correo.\n\n" .
                                 "Saludos,\nEl equipo de SOSMEX Cursos";

                $mail->send();
                $response['success'] = true;
                $response['message'] = 'Si tu correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.';
            } catch (PHPMailerException $e) {
                $response['message'] = 'No se pudo enviar el correo de restablecimiento. Inténtalo más tarde. Mailer Error: ' . $mail->ErrorInfo;
                error_log("Mailer Error in forgot_password for $email: {$mail->ErrorInfo}");
            }
        } else {
            $response['message'] = 'Error al guardar el token de restablecimiento: ' . $stmt_update_token->error;
            error_log("DB Execute Error (update_token) in forgot_password for user_id $userId: " . $stmt_update_token->error);
        }
        $stmt_update_token->close();
    } else {
        // Email no encontrado, pero enviamos un mensaje genérico por seguridad (para evitar enumeración de emails)
        $response['success'] = true; // Aún se considera "éxito" desde la perspectiva del flujo del usuario
        $response['message'] = 'Si tu correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.';
    }
    $stmt_check_email->close();
} else {
    $response['message'] = 'Método de solicitud no válido.';
}

if (isset($db_connection) && $db_connection instanceof mysqli && $db_connection->thread_id) {
    $db_connection->close();
}
echo json_encode($response);
?>