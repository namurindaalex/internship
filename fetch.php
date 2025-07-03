<?php
// Database configuration
$host = 'localhost';
$dbname = 'power_monitor';
$username = 'root';
$password = 'Alex@mysql123';

// Create a PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Get the input parameters from the HTTP GET request
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : null;
$voltage = isset($_GET['voltage']) ? $_GET['voltage'] : null;
$current = isset($_GET['current']) ? $_GET['current'] : null;
$power = isset($_GET['power']) ? $_GET['power'] : null;
$energy = isset($_GET['energy']) ? $_GET['energy'] : null;

// Validate that room_id is set
if (!$room_id) {
    echo "Room ID is missing.";
    exit;
}

// Prepare SQL statement to update the room's data
$sql = "UPDATE rooms 
        SET voltage = :voltage, current = :current, power = :power, energy_consumed = :energy, updated_at = CURRENT_TIMESTAMP
        WHERE room_id = :room_id";

$stmt = $pdo->prepare($sql);

// Bind the parameters to the SQL query
$stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$stmt->bindParam(':voltage', $voltage, PDO::PARAM_STR);
$stmt->bindParam(':current', $current, PDO::PARAM_STR);
$stmt->bindParam(':power', $power, PDO::PARAM_STR);
$stmt->bindParam(':energy', $energy, PDO::PARAM_STR);

// Execute the query
if ($stmt->execute()) {
    echo "Data updated successfully for Room ID: $room_id";
} else {
    echo "Error updating data for Room ID: $room_id";
}
?>