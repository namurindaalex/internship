<?php
require 'db_creation.php';
include 'db_connection.php';

// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Alex@mysql123';
$db_name = 'power_monitor'; 

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all rooms and their power metrics from the single table
    $query = "SELECT room_id, name, power_status, voltage, current, power, energy_consumed 
              FROM rooms 
              ORDER BY updated_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize variables to calculate total power, average voltage, and active rooms
    $totalEnergy = 0;
    $totalVoltage = 0;
    $activeRooms = 0;
    $totalRooms = count($rooms);

    foreach ($rooms as $room) {
        // Total Power is the sum of the 'power' field (not 'energy_consumed')
        $totalEnergy += $room['energy_consumed'];

        // Total Voltage is the sum of 'voltage' for average calculation
        $totalVoltage += $room['voltage'];

        // Count active rooms
        if ($room['power_status'] == 'ON') {
            $activeRooms++;
        }
    }

    // Calculate average voltage
    $averageVoltage = ($totalRooms > 0) ? $totalVoltage / $totalRooms : 0;
    $totalPower = number_format($totalEnergy, 2); // Total power formatted to 2 decimal places
    $averageVoltage = number_format($averageVoltage, 2); // Average voltage formatted to 2 decimal places

} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

<script>
    // Pass PHP array of rooms with energy consumption to JavaScript
    <?php
    echo 'var roomsData = ' . json_encode($rooms) . ';';
    ?>
</script>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Power Monitoring Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/plotly.js/2.24.2/plotly.min.js"></script>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .dashboard-title {
            display: flex;
            align-items: center;
        }

        .dashboard-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .dashboard-title .icon {
            margin-right: 10px;
            font-size: 24px;
            color: var(--primary-color);
        }

        .top-controls {
            display: flex;
            gap: 15px;
        }

        .refresh-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .refresh-btn:hover {
            background-color: #2980b9;
        }

        .refresh-btn svg {
            margin-right: 6px;
        }

        .last-updated {
            color: #666;
            font-size: 14px;
            align-self: center;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .stat-card .unit {
            font-size: 14px;
            color: #666;
            margin-left: 4px;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }

        .room-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--dark-color);
            color: white;
        }

        .room-name {
            font-size: 18px;
            font-weight: 500;
        }

        .room-status {
            font-size: 14px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-on {
            background-color: var(--secondary-color);
        }

        .status-off {
            background-color: var(--danger-color);
        }

        .room-body {
            padding: 20px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .metric-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .metric-box h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .metric-box .metric-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .metric-box .metric-unit {
            font-size: 12px;
            color: #666;
            margin-left: 2px;
        }

        .gauge-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .gauge-container {
            height: 150px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
        }

        .power-switch {
            display: flex;
            align-items: center;
        }

        .power-switch label {
            margin-right: 10px;
            font-weight: 500;
            color: #666;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: var(--secondary-color);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
        }

        .details-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .details-btn:hover {
            background-color: #2980b9;
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .rooms-grid {
                grid-template-columns: 1fr;
            }

            .summary-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="dashboard-title">
                <div class="icon">⚡</div>
                <h1>Room Power Monitoring Dashboard</h1>
            </div>
            <div class="top-controls">
                <span class="last-updated">Last updated: <span id="updateTime">Today, 12:45 PM</span></span>
                <button class="refresh-btn" id="refreshBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                    </svg>
                    Refresh
                </button>
            </div>
        </header>

        <!-- Displaying Summary Stats -->
        <div class="summary-stats">
            <div class="stat-card">
                <h3>TOTAL ENERGY CONSUMPTION</h3>
                <span class="value"><?php echo $totalPower; ?></span><span class="unit">kWh</span>
            </div>
            <div class="stat-card">
                <h3>AVERAGE VOLTAGE</h3>
                <span class="value"><?php echo $averageVoltage; ?></span><span class="unit">V</span>
            </div>
            <!-- <div class="stat-card">
                <h3>PEAK CURRENT</h3>
                <span class="value"><?php echo $peakCurrent; ?></span><span class="unit">A</span>
            </div> -->
            <div class="stat-card">
                <h3>ACTIVE ROOMS</h3>
                <span class="value"><?php echo $activeRooms; ?></span><span class="unit">/ <?php echo $totalRooms; ?></span>
            </div>
        </div>


        <div class="rooms-grid" id="roomWidgets">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <div class="room-header">
                        <div class="room-name"><?php echo htmlspecialchars($room['name']); ?></div>
                        <div class="room-status <?php echo $room['power_status'] == 'ON' ? 'status-on' : 'status-off'; ?>">
                            <?php echo $room['power_status'] == 'ON' ? 'ACTIVE' : 'INACTIVE'; ?>
                        </div>
                    </div>
                    <div class="room-body">
                        <div class="metrics-grid">
                            <div class="metric-box">
                                <h4>VOLTAGE</h4>
                                <span class="metric-value"><?php echo $room['voltage'] ?? '0'; ?></span><span class="metric-unit">V</span>
                            </div>
                            <div class="metric-box">
                                <h4>CURRENT</h4>
                                <span class="metric-value"><?php echo $room['current'] ?? '0'; ?></span><span class="metric-unit">A</span>
                            </div>
                            <div class="metric-box">
                                <h4>POWER</h4>
                                <span class="metric-value"><?php echo $room['power'] ?? '0'; ?></span><span class="metric-unit">W</span>
                            </div>
                            <div class="metric-box">
                                <h4>ENERGY CONSUMED</h4>
                                <span class="metric-value"><?php echo $room['energy_consumed'] ?? '0'; ?></span><span class="metric-unit">kWh</span>
                            </div>
                        </div>
                        <div class="gauge-grid">
                            <div class="gauge-container" id="powerGauge<?php echo $room['room_id']; ?>"></div>
                        </div>
                        <div class="controls">
                            <div class="power-switch">
                                <label>Power Control:</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="powerToggle<?php echo $room['room_id']; ?>"
                                        <?php echo $room['power_status'] == 'ON' ? 'checked' : ''; ?>
                                        onchange="togglePower(<?php echo $room['room_id']; ?>, this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <button class="details-btn" id="viewDetails<?php echo $room['room_id']; ?>">View Details</button>
                        </div>

                        <script>
                            // Function to handle the toggle and make an AJAX request to update the power status
                            function togglePower(roomId, isChecked) {
                                var status = isChecked ? 'ON' : 'OFF'; // Determine the power status based on the checkbox state

                                // Send AJAX request to update the power status in the database
                                var xhr = new XMLHttpRequest();
                                xhr.open("POST", "update_power_status.php", true); // Request to the PHP script
                                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                                // Send the room_id and the new power status to the PHP script
                                xhr.send("room_id=" + roomId + "&status=" + status);

                                // Optionally, update the status on the page immediately
                                var statusElement = document.querySelector("#powerToggle" + roomId).closest('.room-card').querySelector('.room-status');
                                if (status === 'ON') {
                                    statusElement.textContent = 'ACTIVE';
                                    statusElement.classList.add('status-on');
                                    statusElement.classList.remove('status-off');
                                } else {
                                    statusElement.textContent = 'INACTIVE';
                                    statusElement.classList.add('status-off');
                                    statusElement.classList.remove('status-on');
                                }
                            }
                        </script>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <script>
        window.addEventListener('load', function() {
            initGauges();
            setupPowerToggles();
            setupRefreshButton();
            setupViewDetailsButtons();
        });

        // Dynamically initialize power gauges
        function initGauges() {
            // Loop through each room data passed from PHP
            roomsData.forEach(room => {
                let roomId = room.room_id; // Fetch room_id from the data
                let energyConsumed = parseFloat(room.energy_consumed) || 0;

                // Calculate the value to be set in the gauge, use energyConsumed as value
                let gaugeValue = energyConsumed;

                // Determine the color shading based on the energy consumption
                let gaugeColor;
                if (gaugeValue < 1) {
                    gaugeColor = "#2ecc71"; // Green for low consumption
                } else if (gaugeValue >= 1 && gaugeValue < 2) {
                    gaugeColor = "#f39c12"; // Yellow for medium consumption
                } else {
                    gaugeColor = "#e74c3c"; // Red for high consumption
                }

                // Plotly gauge configuration
                Plotly.newPlot(`powerGauge${roomId}`, [{
                    type: 'indicator',
                    mode: 'gauge+number',
                    value: gaugeValue,
                    title: {
                        text: 'Power Consumption',
                        font: {
                            size: 14
                        }
                    },
                    number: {
                        font: {
                            size: 20
                        },
                        suffix: ' kWh', // Assuming energy is in kWh
                        valueformat: '.2f'
                    },
                    gauge: {
                        axis: {
                            range: [0, 100], // Adjust range as per your energy consumption max value
                            tickwidth: 1,
                            tickcolor: "#666"
                        },
                        bar: {
                            color: gaugeColor,
                            thickness: 0.5
                        },
                        bgcolor: "white",
                        borderwidth: 2,
                        bordercolor: "lightgray",
                        steps: [{
                                range: [0, 30],
                                color: "#2ecc71"
                            },
                            {
                                range: [30, 70],
                                color: "#f39c12"
                            },
                            {
                                range: [70, 100],
                                color: "#e74c3c"
                            }
                        ]
                    }
                }], {
                    margin: {
                        t: 25,
                        b: 0,
                        l: 0,
                        r: 0
                    },
                    height: 150
                });
            });
        }

        // Refresh button to update values dynamically
        function setupRefreshButton() {
            document.getElementById('refreshBtn').addEventListener('click', function() {
                console.log('Refreshing data...');

                // Update only the gauge values without altering the order or structure of the room widgets
                document.querySelectorAll('.gauge-container').forEach(container => {
                    let roomId = container.id.replace('powerGauge', ''); // Extract room ID
                    let newValue = Math.random() * 2.5; // New value for demonstration, adjust as needed

                    // Use Plotly.update to update the value of the gauge, keeping the order intact
                    Plotly.update(`powerGauge${roomId}`, {
                        'value': [newValue] // Update only the value
                    });
                });

                updateLastUpdatedTime(); // Update the last updated time
            });
        }

        // Update last updated time
        function updateLastUpdatedTime() {
            const now = new Date();
            const formattedTime = now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            document.getElementById('updateTime').textContent = `Last updated: ${formattedTime}`;
        }

        // View details button event listeners
        function setupViewDetailsButtons() {
            document.querySelectorAll('.details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let roomId = this.id.replace('viewDetails', ''); // Extract room ID from button ID
                    alert(`Viewing detailed statistics for Room ${roomId}`);
                });
            });
        }
    </script>

    <!-- <script>
    function fetchData() {
        fetch("update_current.php") // Replace with the actual PHP script
            .then(response => response.text())
            .then(data => console.log("Data updated:", data))
            .catch(error => console.error("Error fetching data:", error));
    }

    setInterval(fetchData, 50);
</script> -->



</body>

</html>