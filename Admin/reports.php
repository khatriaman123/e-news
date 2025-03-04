<?php
include 'config.php'; // Database connection

// Set default date range (last 30 days)
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-30 days'));
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d');

// Users: Total users and new users within date range
$userQuery = "SELECT COUNT(*) AS total_users, 
                     SUM(CASE WHEN created_at BETWEEN '$date_start' AND '$date_end' THEN 1 ELSE 0 END) AS new_users 
              FROM users";
$userResult = mysqli_query($link, $userQuery);
$userData = mysqli_fetch_assoc($userResult);

// Articles: Total articles, their category, admin (author), and date
$articleQuery = "SELECT a.title, a.created_at AS article_date, a.category, 
                        CONCAT(ad.username) AS admin_name 
                 FROM articles a 
                 JOIN admins ad ON a.author_id = ad.id
                 WHERE a.created_at BETWEEN '$date_start' AND '$date_end' 
                 ORDER BY a.created_at DESC";
$articleResult = mysqli_query($link, $articleQuery);

// Articles: Top authors (most articles posted)
$authorQuery = "SELECT u.username AS author_name, COUNT(a.id) AS article_count 
                FROM articles a
                JOIN admins u ON a.author_id = u.id
                WHERE a.created_at BETWEEN '$date_start' AND '$date_end'
                GROUP BY u.username
                ORDER BY article_count DESC
                LIMIT 5";
$authorResult = mysqli_query($link, $authorQuery);

// Articles: Most viewed articles
$mostViewedQuery = "SELECT title, views 
                    FROM articles 
                    WHERE created_at BETWEEN '$date_start' AND '$date_end' 
                    ORDER BY views DESC 
                    LIMIT 5";
$mostViewedResult = mysqli_query($link, $mostViewedQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3 {
            text-align: center;
            color: #333;
        }
        .report-section {
            background-color: #fff;
            padding: 20px;
            margin: 20px 0;
            margin-left: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-weight: bold;
        }
        input[type="date"], button {
            padding: 10px;
            margin: 0 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div id="sidebar" class="sidebar">

<h2 style="color:white;">Admin Panel</h2>
<ul>
    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="articles.php"><i class="fas fa-newspaper"></i> Articles</a></li>
    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
    <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
    <li><a href="reports.php" class="active"><i class="fas fa-file-alt"></i> Reports</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
</ul>
</div>
    <h1>Detailed Reports: Users & Articles</h1>

    <!-- Date Filter -->
    <form method="GET" action="reports.php">
        <label for="date_start">Start Date:</label>
        <input type="date" id="date_start" name="date_start" value="<?php echo $date_start; ?>">
        <label for="date_end">End Date:</label>
        <input type="date" id="date_end" name="date_end" value="<?php echo $date_end; ?>">
        <button type="submit">Filter</button>
    </form>

    <!-- Users Report -->
    <div class="report-section">
    <h2>Users Report</h2>
    <p><strong>Total Users:</strong> 
        <a href="user_details.php?filter=all"><?php echo $userData['total_users']; ?></a>
    </p>
    <p><strong>New Users (from <?php echo $date_start; ?> to <?php echo $date_end; ?>):</strong> 
        <a href="user_details.php?filter=new&date_start=<?php echo $date_start; ?>&date_end=<?php echo $date_end; ?>">
            <?php echo $userData['new_users']; ?>
        </a>
    </p>
</div>

    <!-- Articles Report -->
    <div class="report-section">
        <h2>Articles Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Posted By (Admin)</th>
                    <th>Date Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($articleResult)): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo $row['admin_name']; ?></td>
                        <td><?php echo date('F j, Y', strtotime($row['article_date'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h3>Authors</h3>
        <table>
            <thead>
                <tr>
                    <th>Author Name</th>
                    <th>Articles Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($authorResult)): ?>
                    <tr>
                        <td><?php echo $row['author_name']; ?></td>
                        <td><?php echo $row['article_count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Most Viewed Articles -->
        <h3>Top 5 Most Viewed Articles</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Views</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($mostViewedResult)): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['views']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
