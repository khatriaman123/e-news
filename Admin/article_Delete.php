<?php
session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$sql = "SELECT * FROM articles";
$result = mysqli_query($link, $sql);

mysqli_close($link);

date_default_timezone_set('Asia/Kolkata');

// Function to get time ago
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diffSeconds = ($diff->y * 365 * 24 * 60 * 60) +
                   ($diff->m * 30 * 24 * 60 * 60) +
                   ($diff->d * 24 * 60 * 60) +
                   ($diff->h * 60 * 60) +
                   ($diff->i * 60) +
                   $diff->s;

    if ($diffSeconds < 60) {
        return $diffSeconds . ' second' . ($diffSeconds != 1 ? 's' : '') . ' ago';
    } elseif ($diffSeconds < 3600) {
        $minutes = floor($diffSeconds / 60);
        return $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';
    } elseif ($diffSeconds < 86400) {
        $hours = floor($diffSeconds / 3600);
        return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
    } elseif ($diffSeconds < 604800) {
        $days = floor($diffSeconds / 86400);
        return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
    } elseif ($diffSeconds < 2419200) {
        $weeks = floor($diffSeconds / 604800);
        return $weeks . ' week' . ($weeks != 1 ? 's' : '') . ' ago';
    } elseif ($diffSeconds < 29030400) {
        $months = floor($diffSeconds / 2419200);
        return $months . ' month' . ($months != 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diffSeconds / 29030400);
        return $years . ' year' . ($years != 1 ? 's' : '') . ' ago';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Articles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="article.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="articles.php"><i class="fas fa-newspaper"></i> Articles</a></li>
            <li class="sub-item" style="margin-left: 30px;"><a href="add_article.php"><i class="fas fa-pencil-alt"></i> Add Article</a></li>
            <li class="sub-item" style="margin-left: 30px;"><a href="article_view.php"><i class="fas fa-eye"></i> View Article</a></li>
            <li class="sub-item" style="margin-left: 30px;"><a href="article_Edit.php"><i class="fas fa-edit"></i> Edit Article</a></li>
            <li class="sub-item" style="margin-left: 30px;"><a href="article_Delete.php" class="active"><i class="fas fa-trash-alt"></i> Delete Article</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <h1>View Articles</h1>
        </header>
        <div class="mt-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php $title = $row['title'];
                                        if (strlen($title) > 50) {
                                            $title = substr($title, 0, 50) . '...';
                                        }
                                        echo htmlspecialchars($title); 
                                    ?>
                                </td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo timeAgo($row['created_at']); ?></td>
                                <td>
                                    <a href="delete_article.php?id=<?php echo $row['id']; ?>&redirect=article_Delete.php" class="btn btn-danger" style="color:white; background-color:red">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No articles found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
