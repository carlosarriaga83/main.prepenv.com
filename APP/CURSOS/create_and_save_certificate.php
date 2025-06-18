<?php
session_start();

require ('/home/u124132715/domains/prepenv.com/public_html/SOSMEX/vendor/autoload.php'); // Composer Autoloader

use setasign\Fpdi\Fpdi;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'filePath' => null];

// Es crucial que user_email también esté en la sesión para enviar cualquier correo.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_full_name']) || !isset($_SESSION['user_email'])) {
    $response['message'] = "Acceso no autorizado o sesión no válida (datos de usuario incompletos).";
    echo json_encode($response);
    exit;
}

$userName = $_SESSION['user_full_name'];
$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email']; // Necesario para ambos tipos de correo
$completionDate = date("d/m/Y");
$templateFile = 'M1.pdf'; // Plantilla de certificado
$certificatesDir = 'certificates/'; // Directorio para guardar los certificados

// --- INICIO: Lógica para verificar aprobación del cuestionario ---
require_once 'sql_config.php'; // Para acceso a la base de datos

define('PASSING_PERCENTAGE', 60.0); // Define el porcentaje mínimo para aprobar (ej. 70%)

if ($db_connection->connect_error) {
    $response['message'] = 'Error de conexión a la base de datos al verificar la aprobación del cuestionario.';
    error_log("DB Connection Error in create_and_save_certificate: " . $db_connection->connect_error);
    echo json_encode($response);
    exit;
}

$stmt_check_approval = $db_connection->prepare("SELECT percentage_score FROM quiz_results WHERE user_id = ? ORDER BY submission_date DESC LIMIT 1");
if (!$stmt_check_approval) {
    $response['message'] = 'Error al preparar la consulta de aprobación: ' . $db_connection->error;
    error_log("DB Prepare Error (check_approval) in create_and_save_certificate: " . $db_connection->error);
    $db_connection->close();
    echo json_encode($response);
    exit;
}

$stmt_check_approval->bind_param("i", $userId);
$stmt_check_approval->execute();
$result_approval = $stmt_check_approval->get_result();
$quiz_result_row = $result_approval->fetch_assoc();
$stmt_check_approval->close();

if (!$quiz_result_row) {
    $response['message'] = "No se encontró un resultado de cuestionario previo para este usuario. No se puede determinar el estado de aprobación.";
    error_log("No quiz result found for user_id: $userId in create_and_save_certificate for approval check.");
    $db_connection->close();
    echo json_encode($response);
    exit;
}

$userPercentageScore = (float)$quiz_result_row['percentage_score'];

// --- INICIO: Función auxiliar para enviar correos ---
function sendNotificationEmail(string $recipientEmail, string $recipientName, string $subject, string $htmlBody, string $altBody, ?string $attachmentPath = null): array {
    $mail = new PHPMailer(true);
    $emailStatus = ['sent' => false, 'errorInfo' => ''];
    try {
        // Configuración SMTP
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Descomentar para depuración
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'constancias@sosmexicoriesgo.com';
        $mail->Password   = 'Pluma123.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->XMailer    = ' '; // Opcional: Ocultar o cambiar el X-Mailer

        $mail->setFrom('constancias@sosmexicoriesgo.com', 'SOSMEX Cursos');
        $mail->addReplyTo('constancias@sosmexicoriesgo.com', 'Soporte SOSMEX Cursos');
        $mail->addAddress($recipientEmail, $recipientName);

        if ($attachmentPath && file_exists($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $altBody;

        $mail->send();
        $emailStatus['sent'] = true;
    } catch (PHPMailerException $e) {
        $emailStatus['errorInfo'] = $mail->ErrorInfo;
        error_log("Mailer Error for $recipientEmail: {$mail->ErrorInfo}");
    }
    return $emailStatus;
}
// --- FIN: Función auxiliar para enviar correos ---

if ($userPercentageScore < PASSING_PERCENTAGE) {
    // El usuario NO aprobó, enviar correo de notificación de no aprobación
    $failSubject = 'Resultado de tu evaluación del curso SOSMEX';
    $failHtmlBody = "Estimado/a " . htmlspecialchars($userName) . ",<br><br>" .
                             "Te informamos sobre el resultado de tu reciente evaluación para el curso.<br><br>" .
                             "Tu puntuación obtenida fue: <b>" . number_format($userPercentageScore, 2) . "%</b>.<br>" .
                             "La puntuación mínima para aprobar es: <b>" . number_format(PASSING_PERCENTAGE, 2) . "%</b>.<br><br>" .
                             "Lamentablemente, en esta ocasión no has alcanzado la puntuación requerida para aprobar.<br>" .
                             "Te animamos a repasar los contenidos del curso y considerar intentarlo nuevamente.<br><br>" .
                             "Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.<br><br>" .
                             "Saludos cordiales,<br>El equipo de SOSMEX Cursos";
    $failAltBody = "Estimado/a " . $userName . ",\n\n" .
                             "Te informamos sobre el resultado de tu reciente evaluación para el curso.\n\n" .
                             "Tu puntuación obtenida fue: " . number_format($userPercentageScore, 2) . "%.\n" .
                             "La puntuación mínima para aprobar es: " . number_format(PASSING_PERCENTAGE, 2) . "%.\n\n" .
                             "Lamentablemente, en esta ocasión no has alcanzado la puntuación requerida para aprobar.\n" .
                             "Te animamos a repasar los contenidos del curso y considerar intentarlo nuevamente.\n\n" .
                             "Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.\n\n" .
                             "Saludos cordiales,\nEl equipo de SOSMEX Cursos";
    
    $emailResult = sendNotificationEmail($userEmail, $userName, $failSubject, $failHtmlBody, $failAltBody);
    if ($emailResult['sent']) {
        $response['message'] = "El cuestionario no fue aprobado (Puntuación: " . number_format($userPercentageScore, 2) . "%). Se ha enviado una notificación por correo.";
        $response['email_status'] = 'Correo de no aprobación enviado.';
    } else {
        $response['message'] = "El cuestionario no fue aprobado (Puntuación: " . number_format($userPercentageScore, 2) . "%), pero hubo un error al enviar el correo de notificación: {$emailResult['errorInfo']}";
        $response['email_status'] = "El correo de no aprobación no pudo ser enviado. Mailer Error: {$emailResult['errorInfo']}";
    }
    $db_connection->close();
    echo json_encode($response); // $response['success'] es false por defecto
    exit;
}
// --- FIN: Lógica para verificar aprobación del cuestionario ---
// Si el script llega aquí, el usuario aprobó. Proceder a generar el certificado.

if (!file_exists($templateFile)) {
    $response['message'] = "Error: Archivo de plantilla PDF no encontrado: " . $templateFile;
    echo json_encode($response);
    exit;
}

// Verificar si el directorio de certificados existe, si no, intentar crearlo.
if (!file_exists($certificatesDir)) {
    // Intentar crear el directorio recursivamente con permisos 0755.
    if (!mkdir($certificatesDir, 0755, true)) {
        $response['message'] = "Error: No se pudo crear el directorio de certificados: " . $certificatesDir;
        error_log("Fallo al crear el directorio de certificados: " . $certificatesDir);
        echo json_encode($response);
        exit;
    }
} elseif (!is_dir($certificatesDir)) {
    // Si la ruta existe pero no es un directorio.
    $response['message'] = "Error: La ruta para los certificados existe pero no es un directorio: " . $certificatesDir;
    error_log("La ruta de certificados existe pero no es un directorio: " . $certificatesDir);
    echo json_encode($response);
    exit;
}

// Verificar si el directorio tiene permisos de escritura.
if (!is_writable($certificatesDir)) {
    $response['message'] = "Error: El directorio de certificados no tiene permisos de escritura: " . $certificatesDir;
    error_log("El directorio de certificados no tiene permisos de escritura: " . $certificatesDir);
    echo json_encode($response);
    exit;
}

// Generar un nombre de archivo único
$fileName = 'Certificado_' . $userId . '_' . time() . '.pdf';
$fullFilePath = $certificatesDir . $fileName;

try {
    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($templateFile);
    $templateId = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($templateId);

    if ($size['orientation'] === 'L') { // o $size['width'] > $size['height']
        $pdf->AddPage('L', [$size['width'], $size['height']]);
    } else {
        $pdf->AddPage('P', [$size['width'], $size['height']]);
    }
    $pdf->useTemplate($templateId);

    // Escribir nombre del participante (ajusta coordenadas y fuente según tu plantilla)
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(50, 100); 
    $pdf->Write(0, utf8_decode($userName));

    // Escribir fecha (ajusta coordenadas y fuente)
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(150, 120); 
    $pdf->Write(0, $completionDate);

    $pdf->Output('F', $fullFilePath); // 'F' para guardar en el servidor

    if (file_exists($fullFilePath)) {
        $response['success'] = true;
        $response['filePath'] = $fullFilePath; // Ruta relativa para acceso web
        $response['message'] = 'Certificado generado y guardado.';

        // Actualizar la base de datos con la ruta del certificado
        $stmt_update_cert_path = $db_connection->prepare("UPDATE quiz_results SET certificate_path = ? WHERE user_id = ? ORDER BY submission_date DESC LIMIT 1");
        if ($stmt_update_cert_path) {
            $stmt_update_cert_path->bind_param("si", $fullFilePath, $userId);
            if ($stmt_update_cert_path->execute()) {
                // Éxito al actualizar la ruta del certificado en la BD
                error_log("Ruta del certificado actualizada en BD para user_id: $userId, path: $fullFilePath");
            } else {
                // Error al ejecutar la actualización
                $response['message'] .= ' (Advertencia: No se pudo actualizar la ruta del certificado en la base de datos: ' . $stmt_update_cert_path->error . ')';
                error_log("Error al actualizar la ruta del certificado en BD para user_id $userId: " . $stmt_update_cert_path->error);
            }
            $stmt_update_cert_path->close();
        } else {
            // Error al preparar la consulta de actualización
            $response['message'] .= ' (Advertencia: No se pudo preparar la actualización de la ruta del certificado en la base de datos: ' . $db_connection->error . ')';
            error_log("Error al preparar la actualización de la ruta del certificado en BD para user_id $userId: " . $db_connection->error);
        }

        // Enviar correo electrónico con el certificado adjunto
        // $userEmail ya está disponible desde el inicio del script
        $successSubject = '¡Felicidades! Tu certificado del curso está listo';
        $successHtmlBody = "Estimado/a " . htmlspecialchars($userName) . ",<br><br>" .
                                 "¡Enhorabuena! Has completado y aprobado el curso satisfactoriamente.<br><br>" .
                                 "Adjunto encontrarás tu certificado de finalización con fecha: " . $completionDate . ".<br><br>" .
                                 "¡Gracias por participar!<br><br>" .
                                 "Saludos cordiales,<br>El equipo de SOSMEX Cursos";
        $successAltBody = "Estimado/a " . $userName . ",\n\n" .
                                 "¡Enhorabuena! Has completado y aprobado el curso satisfactoriamente.\n\n" .
                                 "Adjunto encontrarás tu certificado de finalización con fecha: " . $completionDate . ".\n\n" .
                                 "¡Gracias por participar!\n\n" .
                                 "Saludos cordiales,\nEl equipo de SOSMEX Cursos";
        
        $emailResult = sendNotificationEmail($userEmail, $userName, $successSubject, $successHtmlBody, $successAltBody, $fullFilePath);

        if ($emailResult['sent']) {
            $response['email_status'] = 'Correo de felicitación enviado exitosamente.';
        } else {
            $response['email_status'] = "El correo no pudo ser enviado. Mailer Error: {$emailResult['errorInfo']}";
        }
        // El caso de user_email no en sesión se maneja al inicio.
    } else {
        $response['message'] = 'Error: No se pudo guardar el archivo PDF generado.';
    }
} catch (Exception $e) {
    $response['message'] = "Error al generar el PDF: " . $e->getMessage();
    error_log("PDF Generation Exception: " . $e->getMessage());
}

// Asegurarse de cerrar la conexión a la base de datos si fue abierta.
if (isset($db_connection) && $db_connection instanceof mysqli && $db_connection->thread_id) {
    $db_connection->close();
}

echo json_encode($response);
?>