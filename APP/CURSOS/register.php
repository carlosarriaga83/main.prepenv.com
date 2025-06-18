<?php
require_once 'sql_config.php'; 

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);

    $fullName = $input['fullName'] ?? null;
    $phone = $input['phone'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    // Validación básica
    if (empty($fullName) || empty($phone) || empty($email) || empty($password)) {
        $response['message'] = 'Todos los campos son requeridos.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Formato de correo electrónico inválido.';
        echo json_encode($response);
        exit;
    }

    // Validación de teléfono mexicano (10 dígitos)
    if (!preg_match('/^\d{10}$/', $phone)) {
        // Podrías limpiar el teléfono aquí si esperas formatos como +52XXXXXXXXXX
        // $cleaned_phone = preg_replace('/[^0-9]/', '', $phone);
        // if (substr($cleaned_phone, 0, 2) === "52" && strlen($cleaned_phone) === 12) {
        //     $cleaned_phone = substr($cleaned_phone, 2);
        // }
        // if (!preg_match('/^\d{10}$/', $cleaned_phone)) {
        //    $response['message'] = 'Número de teléfono inválido. Debe contener 10 dígitos.';
        //    echo json_encode($response);
        //    exit;
        // }
        // $phone = $cleaned_phone; // Usar el teléfono limpio
        $response['message'] = 'Número de teléfono inválido. Debe contener 10 dígitos.';
        echo json_encode($response);
        exit;
    }

    if (strlen($password) < 6) {
        $response['message'] = 'La contraseña debe tener al menos 6 caracteres.';
        echo json_encode($response);
        exit;
    }

    // Verificar si el email ya existe
    $stmt_check = $db_connection->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $response['message'] = 'Este correo electrónico ya está registrado.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert = $db_connection->prepare("INSERT INTO users (full_name, phone, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $fullName, $phone, $email, $password_hash);

        if ($stmt_insert->execute()) {
            $response['success'] = true;
            $response['message'] = 'Usuario registrado exitosamente.';
        } else {
            $response['message'] = 'Error al registrar el usuario: ' . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
echo json_encode($response);
mysqli_close($db_connection);
?>