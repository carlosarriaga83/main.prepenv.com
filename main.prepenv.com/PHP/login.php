<?php

// print php error messages
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = trim($_POST['telefono'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($telefono === '' || $password === '') {
        header('Location: ../sign-in.php?error=empty');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, telefono, contrasena FROM usuarios WHERE telefono = :telefono LIMIT 1");
    $stmt->execute(['telefono' => $telefono]);
    $user = $stmt->fetch();

    // If passwords are hashed, use password_verify. If not, use direct comparison.
    if ($user && ($user['contrasena'] === $password || (function_exists('password_verify') && password_verify($password, $user['contrasena'])))) {
        $_SESSION['LOGIN'] = 1;
        $_SESSION['USER'] = [
            'ID' => $user['id'],
            'TELEFONO' => $user['telefono'],
            'NAME' => $user['nombre'] ?? $user['telefono']
        ];
        header('Location: ../index.php');
        exit;
    } else {
        header('Location: ../sign-in.php?error=invalid');
        exit;
    }
} else {
    header('Location: ../sign-in.php');
    exit;
}
?>
