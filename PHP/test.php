

<?php


setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');

function queryToJson($dsn, $username, $password, $sql, $params = []) {
    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert the results to JSON
        return json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (PDOException $e) {
        // Handle error (you can log it or return a specific error message)
        return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Example usage:
$dsn = 'mysql:host=localhost;dbname=u124132715_semaforo;charset=utf8';
$username = 'u124132715_sa1';
$password = 'Pluma123.';
$sql = 'SELECT * FROM Cuentas WHERE id = :id';
$params = [':id' => 2078];

$jsonResult = queryToJson($dsn, $username, $password, $sql, $params);
echo $jsonResult;


?>