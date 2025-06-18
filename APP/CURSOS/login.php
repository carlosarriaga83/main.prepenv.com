<?php 
session_start(); // Iniciar sesión
require_once 'sql_config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    $licenseKey = $input['licenseKey'] ?? null;

    if (empty($email) || empty($password) || empty($licenseKey)) {
        $response['message'] = 'Correo, contraseña y clave de licencia son requeridos.';
        echo json_encode($response);
        exit;
    }

    $stmt = $db_connection->prepare("SELECT id, email, full_name, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // User authenticated, now check license
            $stmt_license = $db_connection->prepare("SELECT id, Datos FROM licencias_JSON WHERE JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.llave')) = ?");
            if (!$stmt_license) {
                $response['message'] = 'Error preparando consulta de licencia: ' . $db_connection->error;
                echo json_encode($response);
                exit;
            }
            $stmt_license->bind_param("s", $licenseKey);
            $stmt_license->execute();
            $result_license = $stmt_license->get_result();

            if ($license_row = $result_license->fetch_assoc()) {
                $license_data_json = $license_row['Datos'];
                $license_data = json_decode($license_data_json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $response['message'] = 'Error al decodificar datos de licencia.';
                } else {
                    $proceed_with_other_checks = true;

                    // 1. Verificar la fecha de expiración de la licencia
                    if (isset($license_data['expira'])) {
                        $expiraDateStr = $license_data['expira'];
                        try {
                            $expiraDate = new DateTime($expiraDateStr);
                            // La licencia es válida durante todo el día de 'expira'.
                            // Expira al comienzo del día *siguiente* a $expiraDateStr.
                            $currentDateStartOfDay = new DateTime('today midnight'); 

                            if ($expiraDate < $currentDateStartOfDay) {
                                $response['message'] = 'Esta licencia ha expirado (' . htmlspecialchars($expiraDateStr) . ').';
                                $proceed_with_other_checks = false;
                            }
                        } catch (Exception $e) {
                            error_log("Error al parsear fecha de expiración para licencia ID {$license_row['id']} ('{$expiraDateStr}'): " . $e->getMessage());
                            $response['message'] = 'Error en el formato de la fecha de expiración de la licencia.';
                            $proceed_with_other_checks = false; // Tratar error de parseo como expirada por seguridad
                        }
                    }
                    // Si 'expira' no está definida en el JSON, se asume que la licencia no está expirada por fecha
                    // y se procede con otras verificaciones (unidades, consumo).

                    if ($proceed_with_other_checks) {
                        // 2. Si no está expirada (o no tiene fecha de expiración), verificar consumo y unidades.
                        // Check if this user has already consumed this specific license
                        $stmt_check_consumption = $db_connection->prepare("SELECT id FROM user_licenses WHERE user_id = ? AND license_json_id = ?");
                        if (!$stmt_check_consumption) {
                            $response['message'] = 'Error preparando consulta de consumo: ' . $db_connection->error;
                        } else {
                            $stmt_check_consumption->bind_param("ii", $user['id'], $license_row['id']);
                            $stmt_check_consumption->execute();
                            $stmt_check_consumption->store_result();
                            $already_consumed = $stmt_check_consumption->num_rows > 0;
                            $stmt_check_consumption->close();

                            if ($already_consumed) {
                                // User has already consumed this license, proceed to login
                                $response['success'] = true;
                            } else {
                                // New user for this license, check availability and consume
                                if (isset($license_data['unidades'], $license_data['usadas']) && $license_data['usadas'] >= $license_data['unidades']) {
                                    $response['message'] = 'No hay unidades disponibles para esta licencia. Usadas: ' . htmlspecialchars($license_data['usadas']) . ', Unidades: ' . htmlspecialchars($license_data['unidades']) . '.';
                                } else {
                                    // License is valid and available, increment 'usadas'
                                    $license_data['usadas'] = (isset($license_data['usadas']) ? (int)$license_data['usadas'] : 0) + 1;
                                    $new_license_data_json = json_encode($license_data);

                                    $stmt_update_license = $db_connection->prepare("UPDATE licencias_JSON SET Datos = ? WHERE id = ?");
                                    if (!$stmt_update_license) {
                                        $response['message'] = 'Error preparando actualización de licencia: ' . $db_connection->error;
                                    } else {
                                        $stmt_update_license->bind_param("si", $new_license_data_json, $license_row['id']);
                                        if ($stmt_update_license->execute()) {
                                            // Record the consumption in user_licenses
                                            $stmt_insert_consumption = $db_connection->prepare("INSERT INTO user_licenses (user_id, license_json_id) VALUES (?, ?)");
                                            if ($stmt_insert_consumption) {
                                                $stmt_insert_consumption->bind_param("ii", $user['id'], $license_row['id']);
                                                $stmt_insert_consumption->execute(); 
                                                $stmt_insert_consumption->close();
                                            } else {
                                                 error_log("Error preparando insert en user_licenses: " . $db_connection->error);
                                            }
                                            $response['success'] = true; // Mark success for login
                                        } else {
                                            $response['message'] = 'Error al actualizar el uso de la licencia: ' . $stmt_update_license->error;
                                        }
                                        $stmt_update_license->close();
                                    }
                                }
                            }

                            if ($response['success']) {
                                 $_SESSION['user_id'] = $user['id'];
                                 $_SESSION['user_email'] = $user['email'];
                                 $_SESSION['user_full_name'] = $user['full_name'];
                                 $_SESSION['license_key_used'] = $licenseKey;
                                 $_SESSION['license_json_id_active'] = $license_row['id']; 
                                 $response['message'] = 'Inicio de sesión exitoso.';
                                 $response['user'] = ['id' => $user['id'], 'email' => $user['email'], 'fullName' => $user['full_name']];
                            }
                        }
                    }
                    // Si $proceed_with_other_checks es false debido a la expiración, 
                    // $response['message'] ya está configurado y $response['success'] sigue siendo false.
                }
            } else {
                $response['message'] = 'Clave de licencia no válida o no encontrada.';
            }
            $stmt_license->close();
        } else {
            $response['message'] = 'Contraseña incorrecta.';
        }
    } else {
        $response['message'] = 'Usuario no encontrado.';
    }
    if (isset($stmt)) {
        $stmt->close();
    }
}
echo json_encode($response);
mysqli_close($db_connection);
?>