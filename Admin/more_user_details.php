<?php
include 'config.php'; // Database connection

// Get the user ID from the URL
$userId = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the user's full details
$userQuery = "SELECT * FROM users WHERE id = $userId";
$userResult = mysqli_query($link, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Fetch the user's activity (e.g., clicked articles), including the category
$activityQuery = "SELECT article_id, title, clicked_at, category FROM user_activity
                  JOIN articles ON user_activity.article_id = articles.id 
                  WHERE user_activity.user_id = $userId";
$activityResult = mysqli_query($link, $activityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Full Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 100%;
            min-width: 300px;
            margin: 20px auto;
            overflow: hidden;
            resize: horizontal;
        }

        h1, h2 {
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
        }

        td {
            background-color: #e9ecef;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        td.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .password-container {
            display: flex;
            align-items: center;
        }

        .toggle-password {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>User Full Details</h1>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p class="password-container">
        <strong>Password:</strong> 
        <span id="password" style="display:none;"><?php echo htmlspecialchars($user['password']); ?></span>
        <span id="password-display" style="display:inline-block;">********</span>
        <span id="password-icon" class="toggle-password" onclick="togglePassword()">👁️</span>
    </p>
    <h2>User Activity</h2>

    <table>
        <thead>
            <tr>
                <th>Article Title</th>
                <th>Category</th>
                <th>Viewed On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($activity = mysqli_fetch_assoc($activityResult)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['title']); ?></td>
                    <td><?php echo htmlspecialchars($activity['category']); ?></td>
                    <td><?php echo date('F j, Y g:i A', strtotime($activity['clicked_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Add a class to show the activity items with animation
    const activityCells = document.querySelectorAll('td');
    activityCells.forEach((cell, index) => {
        setTimeout(() => {
            cell.classList.add('show');
        }, index * 200);
    });
});

function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordDisplay = document.getElementById('password-display');
    const passwordIcon = document.getElementById('password-icon');

    if (passwordField.style.display === "none") {
        passwordField.style.display = "inline";
        passwordDisplay.style.display = "none";
        passwordIcon.textContent = "👁️‍🗨️"; // Change to closed eye icon (or any preferred icon)
    } else {
        passwordField.style.display = "none";
        passwordDisplay.style.display = "inline";
        passwordIcon.textContent = "👁️"; // Change back to open eye icon
    }
}
</script>
</body>
</html>
