<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        die('Username and password required.');
    }

    // check if username already exists
    $lines = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$u] = explode('|', $line);
        if ($u === $username) {
            die('Username already exists.');
        }
    }

    // store username with a hashed password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents('users.txt', $username . '|' . $hash . PHP_EOL, FILE_APPEND | LOCK_EX);

    echo "User '$username' created successfully. <a href='index.html'>Go to login</a>";
}
