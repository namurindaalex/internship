<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Overview</title>
    <link rel="stylesheet" href="admnstyling.css">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        .admin-dashboard {
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .room-container {
            margin-top: 20px;
            padding: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }

        .room-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #007BFF;
        }

        .roomWidgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 90%;
            margin-left: 5%;
        }

        .widget {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: center;
        }

        .widget h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 20px;
        }

        /* Power Toggle Switch Styling */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 50px;
            margin: 10px 0;
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
            height: 40px;
            width: 40px;
            left: 5px;
            bottom: 5px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #2196F3;
        }

        input:focus+.toggle-slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(70px);
        }

        .toggle-status {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
            margin-bottom: 70px;
        }

        .btn {
            text-decoration: none;
            font-size: 1.2rem;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .back-button {
            text-align: center;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .roomWidgets {
                max-width: 100%;
                margin-left: 0;
            }

            .toggle-slider {
                cursor: zoom-out;
            }
        }
    </style>
</head>

<body>
    <div class="admin-dashboard">
        <header class="admin-header">
            <h1>Power Control</h1>
        </header>
        <!-- Room 1 -->
        <div class="room-container">
            <h2 class="room-title">Room 101</h2>
            <div class="roomWidgets" id="roomWidgets1">
                <div class="widget">
                    <h3>Current</h3>
                    <div id="energyConsumptionGauge1"></div>
                </div>
                <div class="widget">
                    <h3>Power</h3>
                    <div id="remainingUnitsGauge1"></div>
                </div>
                <div class="widget">
                    <h3>Room Power Control</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="powerToggle1">
                        <span class="toggle-slider"></span>
                    </label>
                    <div class="toggle-status" id="powerStatus1">Status: OFF</div>
                    <a href="view_details.php?room_id=1" class="btn">View Details</a>
                </div>
            </div>
        </div>

        <!-- Room 2 -->
        <div class="room-container">
            <h2 class="room-title">Room 102</h2>
            <div class="roomWidgets" id="roomWidgets2">
                <div class="widget">
                    <h3>Current</h3>
                    <div id="energyConsumptionGauge2"></div>
                </div>
                <div class="widget">
                    <h3>Power</h3>
                    <div id="remainingUnitsGauge2"></div>
                </div>
                <div class="widget">
                    <h3>Room Power Control</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="powerToggle2">
                        <span class="toggle-slider"></span>
                    </label>
                    <div class="toggle-status" id="powerStatus2">Status: OFF</div>
                    <a href="view_details.php?room_id=2" class="btn">View Details</a>
                </div>
            </div>
        </div>

        <!-- Room 3 -->
        <div class="room-container">
            <h2 class="room-title">Room 103</h2>
            <div class="roomWidgets" id="roomWidgets3">
                <div class="widget">
                    <h3>Current</h3>
                    <div id="energyConsumptionGauge3"></div>
                </div>
                <div class="widget">
                    <h3>Power</h3>
                    <div id="remainingUnitsGauge3"></div>
                </div>
                <div class="widget">
                    <h3>Room Power Control</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="powerToggle3">
                        <span class="toggle-slider"></span>
                    </label>
                    <div class="toggle-status" id="powerStatus3">Status: OFF</div>
                    <a href="view_details.php?room_id=3" class="btn">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Room data - in a real application, you would fetch this from your database
        const roomsData = [{
                room_id: 1,
                name: 'Room 101',
                energy_consumed: 45.75,
                remaining_units: 104.25,
                power_status: 'ON'
            },
            {
                room_id: 2,
                name: 'Room 102',
                energy_consumed: 78.30,
                remaining_units: 71.70,
                power_status: 'OFF'
            },
            {
                room_id: 3,
                name: 'Room 103',
                energy_consumed: 32.50,
                remaining_units: 117.50,
                power_status: 'ON'
            }
        ];

        // Initialize all rooms on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Setup each room
            roomsData.forEach((room, index) => {
                const roomNumber = index + 1;
                setupRoomWidgets(room, roomNumber);
            });
        });

        // Setup gauge charts and toggle for a specific room
        function setupRoomWidgets(room, roomNumber) {
            // Create Energy Consumption Gauge
            Plotly.newPlot(`energyConsumptionGauge${roomNumber}`, [{
                type: 'indicator',
                mode: 'gauge+number',
                value: room.energy_consumed,
                number: {
                    font: {
                        size: 35
                    },
                    suffix: "<span style='font-size:14px;'>kWh</span>",
                    valueformat: ".2f"
                },
                gauge: {
                    axis: {
                        range: [0, 150],
                        tickfont: {
                            size: 15
                        }
                    },
                    steps: [{
                            range: [0, 50],
                            color: "green"
                        },
                        {
                            range: [50, 100],
                            color: "green"
                        },
                        {
                            range: [100, 150],
                            color: "green"
                        }
                    ],
                    bar: {
                        color: "#f6921e",
                        thickness: 0.5
                    }
                }
            }], {
                responsive: true,
                height: 220,
                margin: {
                    t: 20,
                    b: 10,
                    l: 25,
                    r: 45
                }
            });

            // Create Remaining Units Gauge
            Plotly.newPlot(`remainingUnitsGauge${roomNumber}`, [{
                type: 'indicator',
                mode: 'gauge+number',
                value: room.remaining_units,
                number: {
                    font: {
                        size: 35
                    },
                    suffix: "<span style='font-size:14px;'>kWh</span>",
                    valueformat: ".2f"
                },
                gauge: {
                    axis: {
                        range: [0, 150],
                        tickfont: {
                            size: 15
                        }
                    },
                    steps: [{
                            range: [0, 50],
                            color: "green"
                        },
                        {
                            range: [50, 100],
                            color: "green"
                        },
                        {
                            range: [100, 150],
                            color: "green"
                        }
                    ],
                    bar: {
                        color: "#f6921e",
                        thickness: 0.5
                    }
                }
            }], {
                responsive: true,
                height: 220,
                margin: {
                    t: 20,
                    b: 10,
                    l: 25,
                    r: 45
                }
            });

            // Setup power toggle
            const powerToggle = document.getElementById(`powerToggle${roomNumber}`);
            const powerStatus = document.getElementById(`powerStatus${roomNumber}`);

            // Set initial state
            powerToggle.checked = room.power_status === 'ON';
            powerStatus.textContent = `Status: ${room.power_status}`;

            // Add event listener
            powerToggle.addEventListener('change', (e) => {
                const newStatus = e.target.checked ? 'ON' : 'OFF';

                // In a real app, you would send this to your server
                fetch('overview.php?action=update_power_status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `room_id=${room.room_id}&power_status=${newStatus}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        powerStatus.textContent = `Status: ${newStatus}`;
                    })
                    .catch(error => {
                        console.error('Error updating power status:', error);
                        // Revert toggle if update fails
                        powerToggle.checked = !e.target.checked;
                    });
            });
        }
    </script>
</body>

</html>