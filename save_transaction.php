<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

// ✅ Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}

// ✅ Database connection
$conn = new mysqli('localhost', 'root', '', 'fintrack_db');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// ✅ Add new transaction (POST request)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username    = $_SESSION['username'];
    $description = htmlspecialchars(trim($_POST['description']));
    $category    = htmlspecialchars(trim($_POST['category']));
    $type        = htmlspecialchars(trim($_POST['type']));
    $amount      = floatval($_POST['amount']);
    $date        = date("Y-m-d"); // current date

    if (empty($description) || empty($category) || empty($type) || $amount <= 0) {
        die("<h3 style='color:red;'>Invalid input data</h3>");
    }

    $stmt = $conn->prepare("INSERT INTO transactions (username, date, description, category, type, amount, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssd", $username, $date, $description, $category, $type, $amount);

    if ($stmt->execute()) {
        // ✅ Save to CSV log file
        $csvFile = 'transactions_log.csv';
        $fileExists = file_exists($csvFile);

        $fp = fopen($csvFile, 'a');
        // Add headers only if file is new
        if (!$fileExists) {
            fputcsv($fp, ['Username', 'Date', 'Description', 'Category', 'Type', 'Amount']);
        }
        fputcsv($fp, [$username, $date, $description, $category, $type, $amount]);
        fclose($fp);

        echo "<script>
            alert('Transaction added successfully!');
            window.location.href = 'dashboard.html';
        </script>";
    } else {
        echo "<h3 style='color:red;'>Error saving transaction: " . $stmt->error . "</h3>";
    }

    $stmt->close();
    $conn->close();
    exit;
}

// ✅ Fetch transactions for dashboard or transaction page
if (isset($_GET['action']) && $_GET['action'] === 'get') {
    $username = $_SESSION['username'];

    $result = $conn->query("SELECT * FROM transactions WHERE username = '$username' ORDER BY date_added DESC");
    $transactions = [];
    $total_income = 0;
    $total_expense = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'Income') $total_income += $row['amount'];
        if ($row['type'] === 'Expense') $total_expense += $row['amount'];
        $transactions[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "transactions" => $transactions,
        "total_income" => $total_income,
        "total_expense" => $total_expense,
        "total_balance" => $total_income - $total_expense
    ]);
    exit;
}

// ✅ Delete transaction
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $username = $_SESSION['username'];
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $id, $username);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Transaction deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete"]);
    }
    exit;
}
?>
