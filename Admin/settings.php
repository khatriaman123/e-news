<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Database connection
require_once "config.php";

// Fetch settings from the database
$settings_query = "SELECT * FROM settings";
$settings_result = mysqli_query($link, $settings_query);

// Initialize the settings array
$settings = [];

if ($settings_result) {
    while ($row = mysqli_fetch_assoc($settings_result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} else {
    die("Error fetching settings: " . mysqli_error($link));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update site name and footer text
    $site_name = $_POST['site_name'];
    $footer_text = $_POST['footer_text'];

    // Update site name
    $update_site_name = "UPDATE settings SET setting_key = ? WHERE setting_value = 'site_name'";
    if ($stmt = mysqli_prepare($link, $update_site_name)) {
        mysqli_stmt_bind_param($stmt, "s", $site_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Update footer text
    $update_footer_text = "UPDATE settings SET setting_key = ? WHERE setting_value = 'footer_text'";
    if ($stmt = mysqli_prepare($link, $update_footer_text)) {
        mysqli_stmt_bind_param($stmt, "s", $footer_text);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Handle logo upload
    if ($_FILES['site_logo']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['site_logo']['name']);
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_file)) {
            $update_site_logo = "UPDATE settings SET setting_key = ? WHERE setting_value = 'site_logo'";
            if ($stmt = mysqli_prepare($link, $update_site_logo)) {
                mysqli_stmt_bind_param($stmt, "s", $target_file);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    header("location: settings.php");
    exit;
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Settings</h2>
        <form action="settings.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" name="site_name" id="site_name" class="form-control" value="<?php echo isset($settings['site_name']) ? htmlspecialchars($settings['site_name']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="footer_text">Footer Text</label>
                <textarea name="footer_text" id="footer_text" class="form-control" required><?php echo isset($settings['footer_text']) ? htmlspecialchars($settings['footer_text']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="site_logo">Site Logo</label>
                <input type="file" name="site_logo" id="site_logo" class="form-control">
                <?php if (isset($settings['site_logo']) && $settings['site_logo']): ?>
                    <img src="<?php echo $settings['site_logo']; ?>" alt="Site Logo" style="max-width: 100px; margin-top: 10px;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</body>
</html>
