<?php
session_start();


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$sql_articles = "SELECT COUNT(*) as count FROM articles";
$result_articles = mysqli_query($link, $sql_articles);
$count_articles = mysqli_fetch_assoc($result_articles)['count'];

$sql_users = "SELECT COUNT(*) as count FROM users";
$result_users = mysqli_query($link, $sql_users);
$count_users = mysqli_fetch_assoc($result_users)['count'];

$sql_settings = "SELECT COUNT(*) as count FROM settings";
$result_settings = mysqli_query($link, $sql_settings);
$count_settings = mysqli_fetch_assoc($result_settings)['count'];

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div id="sidebar" class="sidebar">

        <h2>Admin Panel</h2>
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="articles.php"><i class="fas fa-newspaper"></i> Articles</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div id="mainContent" class="main-content">
        <header>
            <h1>Welcome to the Admin Dashboard</h1>
        </header>
        <div class="info-cards">
            <div class="card">
                <h3>Total Articles</h3>
                <p><?php echo $count_articles; ?></p>
            </div>
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $count_users; ?></p>
            </div>
            <div class="card">
                <h3>Settings</h3>
                <p><?php echo $count_settings; ?></p>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
