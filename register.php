<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$username || !$email || strlen($password) < 6) {
        echo "<script>alert('Invalid Input!'); window.location.href='index.html';</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error: Could not register user.'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
