<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_database_name;charset=utf8', 'your_username', 'your_password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
