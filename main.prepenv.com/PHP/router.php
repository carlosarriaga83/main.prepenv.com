<?php
require_once __DIR__ . '/api.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_uri === '/login' && $request_method === 'POST') {
    $telefono = trim($_POST['telefono'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $response = login($telefono, $password);
    if ($response['success']) {
        header('Location: ../index.php');
    } else {
        header('Location: ../sign-in.php?error=' . $response['error']);
    }
    exit;
}

if ($request_uri === '/logout' && $request_method === 'POST') {
    logout();
    header('Location: ../sign-in.php');
    exit;
}

if ($request_uri === '/user/details' && $request_method === 'GET') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $user_id = $_SESSION['USER']['ID'];
    $user = fetchUserDetails($user_id);
    echo json_encode($user);
    exit;
}

if ($request_uri === '/user/update' && $request_method === 'POST') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $user_id = $_SESSION['USER']['ID'];
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    updateUserDetails($user_id, $nombre, $telefono, $email);
    header('Location: ../user_profile.php?success=updated');
    exit;
}

if ($request_uri === '/user/delete' && $request_method === 'POST') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $user_id = $_SESSION['USER']['ID'];
    deleteUserAccount($user_id);
    header('Location: ../sign-in.php');
    exit;
}

if ($request_uri === '/vehiculo/edit' && $request_method === 'GET') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $vehiculo_id = $_GET['id'] ?? null;
    $user_id = $_SESSION['USER']['ID'];
    if (!$vehiculo_id) {
        header('Location: ../vehiculo_dashboard.php');
        exit;
    }
    $vehiculo = getVehicle($vehiculo_id, $user_id);
    if (!$vehiculo) {
        header('Location: ../vehiculo_dashboard.php');
        exit;
    }
    echo json_encode($vehiculo);
    exit;
}

if ($request_uri === '/vehiculo/edit' && $request_method === 'POST') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $vehiculo_id = $_POST['id'] ?? null;
    $matricula = trim($_POST['matricula'] ?? '');
    $alias = trim($_POST['alias'] ?? '');
    $user_id = $_SESSION['USER']['ID'];
    if ($vehiculo_id && $matricula) {
        try {
            updateVehicle($vehiculo_id, $user_id, $matricula, $alias);
            header('Location: ../vehiculo_dashboard.php');
            exit;
        } catch (Exception $e) {
            header('Location: ../vehiculo_edit.php?id=' . $vehiculo_id . '&error=1');
            exit;
        }
    } else {
        header('Location: ../vehiculo_edit.php?id=' . $vehiculo_id . '&error=1');
        exit;
    }
}

if ($request_uri === '/vehiculo/delete' && $request_method === 'POST') {
    session_start();
    if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] != 1) {
        header('Location: ../sign-in.php');
        exit;
    }
    $vehiculo_id = $_POST['id'] ?? null;
    $user_id = $_SESSION['USER']['ID'];
    if ($vehiculo_id) {
        deleteVehicle($vehiculo_id, $user_id);
    }
    header('Location: ../vehiculo_dashboard.php');
    exit;
}
?>
