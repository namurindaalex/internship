<?php
// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Alex@mysql123';
$db_name = 'power_monitor';

// ThingSpeak API URL (Replace with your Read API Key and Channel ID)
$thingspeak_url = "https://api.thingspeak.com/channels/2873397/feeds.json?api_key=FNNJB2O6L420N83Q&results=1";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the JSON data from ThingSpeak
    $json_data = file_get_contents($thingspeak_url);

    if ($json_data === false) {
        die("Error fetching data from ThingSpeak.");
    }

    // Decode JSON response
    $data = json_decode($json_data, true);

    if (!isset($data['feeds'][0])) {
        die("No data found in ThingSpeak response.");
    }

    // Extract current values from ThingSpeak fields
    $current1 = $data['feeds'][0]['field1'] ?? 0;
    $current2 = $data['feeds'][0]['field2'] ?? 0;
    $current3 = $data['feeds'][0]['field3'] ?? 0;

    // Update the current values in the rooms table
    $updateQuery = "UPDATE rooms SET current = CASE 
                    WHEN name = 'Line 1' THEN :current1 
                    WHEN name = 'Line 2' THEN :current2 
                    WHEN name = 'Line 3' THEN :current3 
                    ELSE current END";

    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':current1', $current1);
    $stmt->bindParam(':current2', $current2);
    $stmt->bindParam(':current3', $current3);

    $stmt->execute();

    echo "Current values updated successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
