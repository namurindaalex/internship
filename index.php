<?php
session_start();
include 'db_connection.php';

$error_message = ""; // Initialize error_message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch user data based on the username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit; // Always exit after a redirect
    } else {
        $error_message = "Invalid login!"; // Set error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Login Container */
        .login-container {
            background-color: white;
            padding: 20px 20px 30px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }

        /* Logo Section */
        .logo-container {
            margin-bottom: 10px;
        }

        .logo {
            width: 190px;
            height: auto;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        /* Input Fields */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #1b75bb;
            outline: none;
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background-color: #f6921e;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color:rgb(245, 161, 65);
        }

        /* Error message styling */
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
            text-align: center;
            display: none;
        }

        /* Responsive Design */
        @media screen and (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            .logo {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php
        if (isset($error_message) && !empty($error_message)) {
            echo "<div id='error-message' class='error'>$error_message</div>";
        }
        ?>
        <div class="logo-container">
            <img src="logo1.png" alt="Logo" class="logo">
        </div>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        <?php if (isset($error_message) && !empty($error_message)): ?>
            setTimeout(function() {
                document.getElementById('error-message').style.display = 'none';
            }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
