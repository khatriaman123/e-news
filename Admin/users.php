<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="user.css">
</head>
<body>
<div class="sidebar">
        <h2 style="color:white;">Admin Panel</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="articles.php"><i class="fas fa-newspaper"></i> Articles</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
            <li style="margin-left: 30px;"><a href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a></li>
            <li style="margin-left: 30px;"><a href="user_view.php"><i class="fas fa-eye"></i> View User</a></li>
            <li style="margin-left: 30px;"><a href="user_edit.php"><i class="fas fa-user-edit"></i> Edit User</a></li>
            <li style="margin-left: 30px;"><a href="user_delete.php"><i class="fas fa-trash-alt"></i> Delete User</a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container mt-5">
        <h2>Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['profile_image'])): ?>
                            <img src="../User/uploads/<?php echo $row['profile_image']; ?>" class="user-image" alt="User Image">
                            <?php else: ?>
                            <img src="User/Profile-PNG-Images.png" class="user-image" alt="Default Image">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="view_user.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View</a>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
