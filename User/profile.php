<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

$activityQuery = "SELECT article_id, title, clicked_at, category FROM user_activity 
                  JOIN articles ON user_activity.article_id = articles.id 
                  WHERE user_activity.user_id = ?
                  ORDER BY user_activity.clicked_at DESC";
$activityStmt = $link->prepare($activityQuery);
$activityStmt->bind_param('i', $user_id);
$activityStmt->execute();
$activityResult = $activityStmt->get_result();

$profile_image_url = "User/Profile-PNG-Images.png";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_image = $_FILES['profile_image']['name'];
    
    if ($profile_image) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($profile_image);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image_url = $target_file;
        } else {
            echo "Error uploading file.";
        }
    } else {
        $profile_image_url = $user['profile_image'];
    }

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, profile_image = ? WHERE id = ?";
$stmt = $link->prepare($sql);

$stmt->bind_param('sssssi', $first_name, $last_name, $email, $password, $profile_image_url, $user_id);

if ($stmt->execute()) {
    header("Location: profile.php");
    exit;
} else {
    echo "Error updating profile: " . $link->error;
}
} else {
    if (!empty($user['profile_image'])) {
        $profile_image_url = "uploads/" . $user['profile_image'];
    }
}

$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

if ($loggedIn && empty($_SESSION['profile_image'])) {
    $sql = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['profile_image'] = $user['profile_image'];
    }
    $stmt->close();
}

$profileImage = isset($_SESSION['profile_image']) && $_SESSION['profile_image'] ? $_SESSION['profile_image'] : 'Profile-PNG-Images.png';
$profileImagePath = "../User/uploads/" . htmlspecialchars($profileImage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-container img {
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
            width: 150px;
            height: 150px;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
        }

        .navbar-nav {
            
            margin-left: auto;
            margin-right: auto;
        }
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: #343a40;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    height: 80px;
}

body {
    padding-top: 70px;
}

.navbar-brand {
    margin-right: auto;
}

.navbar-nav .nav-item {
    text-align: center;
}

.navbar-nav .nav-link.active {
    background-color: #495057;
    color: #ffc107;
    border-bottom: 4px solid #ffc107;
}


        .navbar-logo {
            margin-right: auto;
        }

        .navbar-logout {
            margin-left: auto;
        }
        .table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 1350px;
            min-width: 300px;
            margin: 20px auto;
            overflow: hidden;
            resize: horizontal;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #dee2e6;
    color: #333; /* Text color for table cells */
}

th {
    background-color: #f8f9fa; /* Header background color */
    color: #333; /* Header text color */
}

td {
    background-color: #e9ecef; /* Cell background color */
    opacity: 1; /* Ensure cells are fully visible */
    transition: opacity 0.5s ease; /* Add transition for animation */
}

.text-center {
    text-align: center;
}

.table a {
    text-decoration: none; /* Remove underline */
    color: #007bff; /* Link color */
    transition: color 0.3s, background-color 0.3s; /* Smooth transition */
}

.table a:hover {
    color: #fff; /* Change text color on hover */
    background-color: #007bff; /* Change background color on hover */
    border-radius: 5px; /* Rounded corners */
    padding: 3px; /* Add some padding for better appearance */
}

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark navigation">
        <div class="container">
            <a class="navbar-brand navbar-logo" href="index.php">
                <img src="default-monochrome-white.png" alt="Logo" height="50px">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home" style="color: white;"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle" style="color: white;"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-phone-alt" style="color: white;"></i> Contact Us</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="fas fa-user" style="color: white;"></i> Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo $profileImagePath; ?>" alt="Profile Icon" style="width: 24px; height: 24px; border-radius: 50%;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <?php if ($loggedIn): ?>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            <?php else: ?>
                                <a class="dropdown-item" href="register.php">Register</a>
                                <a class="dropdown-item" href="login.php">Login</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container profile-container">
        <h2 class="text-center mb-4">User Profile</h2>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="text-center">
                <img src="<?php echo htmlspecialchars($profile_image_url); ?>" alt="Profile Image">
            </div>

            <button type="submit" class="btn btn-custom btn-block mt-4">Update Profile</button>
        </form>
    </div>

    <h2 class="text-center mt-5">My Activity</h2>
<table class="table">
    <thead>
        <tr>
            <th>Article Title</th>
            <th>Category</th>
            <th>Viewed On</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($activity = $activityResult->fetch_assoc()): ?>
            <tr>
                <td>
                    <a href="show_article.php?id=<?php echo htmlspecialchars($activity['article_id']); ?>">
                        <?php echo htmlspecialchars($activity['title']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($activity['category']); ?></td>
                <td><?php echo date('F j, Y g:i A', strtotime($activity['clicked_at'])); ?></td>
            </tr>
        <?php endwhile; ?>
        <?php if ($activityResult->num_rows === 0): ?>
            <tr>
                <td colspan="3" class="text-center">No activity found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
