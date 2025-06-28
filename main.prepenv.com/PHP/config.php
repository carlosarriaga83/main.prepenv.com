

<?php


$host = 'localhost';
$db   = 'u124132715_parking_db'; // Cambia por el nombre de tu BD
$user = 'u124132715_parking'; // Cambia por tu usuario de BD
$pass = 'Pellu8aa1!'; // Cambia por tu contraseña de BD
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Línea añadida: Asegura que la conexión con la BD use UTC.
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'"
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     http_response_code(500);
     echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
     exit;
}


?>

	
