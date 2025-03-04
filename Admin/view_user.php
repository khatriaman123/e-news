<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql = "SELECT id, first_name, last_name, email, password, profile_image, created_at FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $user_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
            } else {
                header("location: users.php");
                exit;
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("location: users.php");
    exit;
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 30px;
        }
        .user-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid #007bff;
        }
        .card-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .card-text {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .password-container {
            position: relative;
            margin-bottom: 20px;
        }
        .password-container input {
            padding-right: 40px;
            border-radius: 25px;
            border: 1px solid #ced4da;
        }
        .password-container .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #007bff;
        }
        .btn {
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
            border-radius: 25px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>View User</h2>
        <div class="card">
            <div class="card-body">
                <div class="text-left">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="../user/uploads/<?php echo $user['profile_image']; ?>" class="user-image" alt="User Image">
                    <?php else: ?>
                        <img src="images (1).png" class="user-image" alt="Default Image">
                    <?php endif; ?>
                </div>
                <h5 class="card-title"><?php echo $user['first_name'] . " " . $user['last_name']; ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p class="card-text"><strong>Created At:</strong> <?php echo $user['created_at']; ?></p>
                <div class="password-container">
                    <input type="password" id="password" class="form-control" value="<?php echo htmlspecialchars($user['password']); ?>" readonly>
                    <span class="eye-icon" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="users.php" class="btn btn-primary">Back to Users</a>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.querySelector('.eye-icon i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
