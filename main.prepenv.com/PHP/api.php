<?php
require_once __DIR__ . '/config.php';

function login($telefono, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE telefono = :telefono LIMIT 1");
    $stmt->execute(['telefono' => $telefono]);
    $user = $stmt->fetch();

    if ($user && ($user['contrasena'] === $password || (function_exists('password_verify') && password_verify($password, $user['contrasena'])))) {
        session_start();
        $_SESSION['LOGIN'] = 1;
        $_SESSION['USER'] = [
            'ID' => $user['id'],
            'TELEFONO' => $user['telefono'],
            'NAME' => $user['nombre'] ?? $user['telefono']
        ];
        return ['success' => true];
    }
    return ['success' => false, 'error' => 'invalid'];
}

function logout() {
    session_start();
    session_unset();
    session_destroy();
    return ['success' => true];
}

function fetchUserDetails($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, telefono, email FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }
        return ['success' => true, 'data' => $user];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function updateUserDetails($user_id, $telefono, $email) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuarios SET telefono = ?, email = ? WHERE id = ?");
    $stmt->execute([$telefono, $email ?: null, $user_id]);
    return ['success' => true];
}

function deleteUserAccount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    session_start();
    session_unset();
    session_destroy();
    return ['success' => true];
}

function fetchVehicles($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, matricula, alias FROM vehiculos_usuario WHERE id_usuario = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addVehicle($user_id, $matricula, $alias) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO vehiculos_usuario (id_usuario, matricula, alias) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $matricula, $alias]);
    return ['success' => true];
}

function getVehicle($vehiculo_id, $user_id) {
    global $pdo;
    // vehiculos_usuario: id, id_usuario, matricula, alias
    $stmt = $pdo->prepare("SELECT id, matricula, alias FROM vehiculos_usuario WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$vehiculo_id, $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateVehicle($vehiculo_id, $user_id, $matricula, $alias) {
    global $pdo;
    // vehiculos_usuario: id, id_usuario, matricula, alias
    $stmt = $pdo->prepare("UPDATE vehiculos_usuario SET matricula = ?, alias = ? WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$matricula, $alias, $vehiculo_id, $user_id]);
    return ['success' => true];
}

function deleteVehicle($vehiculo_id, $user_id) {
    global $pdo;
    // vehiculos_usuario: id, id_usuario, matricula, alias
    $stmt = $pdo->prepare("DELETE FROM vehiculos_usuario WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$vehiculo_id, $user_id]);
    return ['success' => true];
}

function fetchReservas($user_id) {
    global $pdo;
    // registros_estacionamiento: id, id_usuario, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id, created_at
    $stmt = $pdo->prepare("SELECT id, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id, created_at FROM registros_estacionamiento WHERE id_usuario = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addReserva($user_id, $matricula, $fecha_hora_entrada, $fecha_hora_salida, $linea_asignada, $espacio_asignado, $estado, $parking_id) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO registros_estacionamiento (id_usuario, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $matricula,
        $fecha_hora_entrada,
        $fecha_hora_salida ?: null,
        $linea_asignada ?: null,
        $espacio_asignado ?: null,
        $estado,
        $parking_id
    ]);
    return ['success' => true];
}

function getReserva($reserva_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, matricula, fecha_hora_entrada, fecha_hora_salida, linea_asignada, espacio_asignado, estado, parking_id, created_at FROM registros_estacionamiento WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$reserva_id, $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateReserva($reserva_id, $user_id, $matricula, $fecha_hora_entrada, $fecha_hora_salida, $linea_asignada, $espacio_asignado, $estado, $parking_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE registros_estacionamiento SET matricula = ?, fecha_hora_entrada = ?, fecha_hora_salida = ?, linea_asignada = ?, espacio_asignado = ?, estado = ?, parking_id = ? WHERE id = ? AND id_usuario = ?");
    $stmt->execute([
        $matricula,
        $fecha_hora_entrada,
        $fecha_hora_salida ?: null,
        $linea_asignada ?: null,
        $espacio_asignado ?: null,
        $estado,
        $parking_id,
        $reserva_id,
        $user_id
    ]);
    return ['success' => true];
}

function deleteReserva($reserva_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM registros_estacionamiento WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$reserva_id, $user_id]);
    return ['success' => true];
}

function fetchParkingLots() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, nombre, direccion, capacidad, estado FROM parking_lots");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addParkingLot($nombre, $direccion, $capacidad, $estado) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO parking_lots (nombre, direccion, capacidad, estado) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $direccion, $capacidad, $estado]);
    return ['success' => true];
}

function getParkingLot($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, nombre, direccion, capacidad, estado FROM parking_lots WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateParkingLot($id, $nombre, $direccion, $capacidad, $estado) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE parking_lots SET nombre = ?, direccion = ?, capacidad = ?, estado = ? WHERE id = ?");
    $stmt->execute([$nombre, $direccion, $capacidad, $estado, $id]);
    return ['success' => true];
}

function deleteParkingLot($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM parking_lots WHERE id = ?");
    $stmt->execute([$id]);
    return ['success' => true];
}
?>
