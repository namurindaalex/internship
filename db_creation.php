<?php
// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Alex@mysql123';
$db_name = 'power_monitor';

try {
    // Connect to MySQL server without specifying a database
    $pdo = new PDO("mysql:host=$db_host;", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists, if not, create it
    $createDbQuery = "CREATE DATABASE IF NOT EXISTS $db_name";
    $pdo->exec($createDbQuery);

    // Connect to the newly created database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the combined "rooms" table
    $createRoomsTableQuery = "
    CREATE TABLE IF NOT EXISTS rooms (
        room_id VARCHAR(50) NOT NULL PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        description TEXT,
        location VARCHAR(100),
        power_status ENUM('ON', 'OFF') DEFAULT 'OFF',
        voltage DECIMAL(10, 2) DEFAULT 0.00,
        current DECIMAL(10, 2) DEFAULT 0.00,
        power DECIMAL(10, 2) DEFAULT 0.00,
        energy_consumed DECIMAL(10, 2) DEFAULT 0.00,
        previous_status ENUM('ON', 'OFF') DEFAULT NULL,
        changed_by VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
    ";
    $pdo->exec($createRoomsTableQuery);

} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
