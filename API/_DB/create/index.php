

<?php 

setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');

	
function executeSQLFile($filePath) {
    // Check if file exists and is readable
    if (!file_exists($filePath) || !is_readable($filePath)) {
        throw new Exception("File not found or not readable: " . $filePath);
    }

    // MySQL host
    $mysql_host = 'localhost';
    // MySQL username
    $mysql_username = 'u124132715_db';
    // MySQL password
    $mysql_password = 'Pellu8aa';
    // Database name
    $mysql_database = 'u124132715_DB';

    // Connect to MySQL server
    try {
		echo 'Connecting.....' . "\n";
        $conn = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
    } catch (Exception $e) {
		echo $e->getMessage();
        throw new Exception("Connection failed: " . $e->getMessage());
    }

    // Check connection
    if ($conn->connect_error) {
		echo 'Connecting2 DB.....' . "\n";
		echo $conn->connect_error;
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
	
	echo 'Connected OK.....' . "\n";
	
    // Temporary variable, used to store current query
    $templine = '';
    // Read in entire file
    $lines = file($filePath);
    // Loop through each line
    foreach ($lines as $line) {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;
        // Add this line to the current segment
        $templine .= $line;
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {
            // Perform the query
            try {
                if(!$conn->query($templine)){
					echo 'Error performing query \'<strong>' . $templine . '\': ' . $conn->error . '<br /><br />'  .  "\n";
                    throw new Exception('Error performing query \'<strong>' . $templine . '\': ' . $conn->error . '<br /><br />');
                }
            } catch (Exception $e) {
				echo 'Error performing query: ' . $e->getMessage() .  "\n";
                throw new Exception('Error performing query: ' . $e->getMessage());
            }
            // Reset temp variable to empty
            $templine = '';
        }
    }
    echo "Tables imported successfully";
    $conn->close();
}
	

echo 'Creating DB.....' . "\n";
executeSQLFile('baseline_DB.sql');
echo 'OK';

?>