<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "news";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$email = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT email, passwd FROM admin_login WHERE email = ?");
$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($email_db, $password_db);

    $stmt->fetch();

    if (password_verify($password, $password_db)) {
        $_SESSION['email'] = $email_db;

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}

$stmt->close();
$conn->close();
?>
w