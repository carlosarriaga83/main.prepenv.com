<?php
session_start(); 
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'fullName' => $_SESSION['user_full_name'] ?? ''
        ]
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
exit;
?>