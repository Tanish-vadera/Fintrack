<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// Connect to MySQL
$conn = new mysqli('localhost', 'root', '', 'fintrack_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $captchaInput = $_POST['captcha'] ?? '';
    $captchaAnswer = $_POST['captchaAnswer'] ?? '';

    // --- CAPTCHA validation ---
    if ($captchaInput != $captchaAnswer) {
        echo "<h2 style='color:red;text-align:center;'>Invalid CAPTCHA. Please try again.</h2>";
        echo "<p style='text-align:center;'><a href='index.html'>Back to Login</a></p>";
        exit;
    }

    // Prepare query securely
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Verify the hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['username']   = $username;
            $_SESSION['login_time'] = date("Y-m-d H:i:s");
            $sessionId              = session_id();

            // Log session in file
            $entry = $username . '|' . $sessionId . '|' . $_SESSION['login_time'] . '|--' . PHP_EOL;
            file_put_contents('session.txt', $entry, FILE_APPEND | LOCK_EX);

            // Redirect to dashboard
            header("Location: dashboard.html");
            exit;
        } else {
            // Invalid password
            echo "<h2 style='color:red;text-align:center;'>Incorrect password</h2>";
            echo "<p style='text-align:center;'><a href='index.html'>Back to Login</a></p>";
            exit;
        }
    } else {
        // User not found
        echo "<h2 style='color:red;text-align:center;'>User not found</h2>";
        echo "<p style='text-align:center;'><a href='index.html'>Back to Login</a></p>";
        exit;
    }
}

$conn->close();
?>
