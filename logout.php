<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

if (isset($_SESSION['username'])) {
    $sessionId   = session_id();
    $logout_time = date("Y-m-d H:i:s");
    $file        = 'session.txt';

    // Check if session.txt exists
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updated = false;

        foreach ($lines as &$line) {
            $parts = explode('|', $line);

            // Ensure line has enough parts and match session ID
            if (isset($parts[1]) && trim($parts[1]) === $sessionId) {
                // Update logout time at the correct position
                $parts[3] = $logout_time;
                $line = implode('|', $parts);
                $updated = true;
                break;
            }
        }

        // If session found and updated, rewrite the file
        if ($updated) {
            file_put_contents($file, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
        } else {
            // In case no matching session found, just append a new line for reference
            file_put_contents($file, "Unknown Session | $sessionId | -- | $logout_time" . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    } else {
        // If no file found, create one
        file_put_contents($file, "No previous sessions found | $sessionId | -- | $logout_time" . PHP_EOL, LOCK_EX);
    }
}

// Destroy PHP session
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.html");
exit;
?>
