<?php
// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Alex@mysql123';
$db_name = 'power_monitor';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $room_id = $_POST['room_id'] ?? '';
        $current = $_POST['current'] ?? 0;
        $voltage = $_POST['voltage'] ?? 230;
        $power = $_POST['power'] ?? 0;

        if (!empty($room_id)) {
            $query = "UPDATE rooms SET current = :current, voltage = :voltage, power = :power, updated_at = NOW() WHERE room_id = :room_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':current' => $current,
                ':voltage' => $voltage,
                ':power' => $power,
                ':room_id' => $room_id
            ]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Room data updated"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Room not found"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid room_id"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
