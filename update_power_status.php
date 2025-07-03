<?php
// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Alex@mysql123';
$db_name = 'power_monitor';

// Check if the necessary parameters are provided
if (isset($_POST['room_id']) && isset($_POST['status'])) {
    $room_id = $_POST['room_id'];
    $status = $_POST['status'];

    try {
        // Create a PDO instance
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update the power status in the database
        $query = "UPDATE rooms SET power_status = :status WHERE room_id = :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();

        // Return a success response
        echo "Power status updated successfully!";
    } catch (PDOException $e) {
        // Return an error message
        echo "Error: " . $e->getMessage();
    }
}
?>
