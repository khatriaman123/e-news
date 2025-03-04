<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "config.php";

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $image = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $image = $_FILES['profile_image']['name'];
        $target_dir = "../user/uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["profile_image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit;
        }

        $allowed_formats = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }

        if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    $sql = "INSERT INTO users (first_name, last_name, email, password, profile_image) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $param_first_name, $param_last_name, $param_email, $param_password, $param_image);
        
        $param_first_name = $firstname;
        $param_last_name = $lastname;
        $param_email = $email;
        $param_password = $password;
        $param_image = $image;
        
        if (mysqli_stmt_execute($stmt)) {
            header("location: users.php");
            exit;
        } else {
            echo "Error: Could not execute query. Please try again later.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare query. Please try again later.";
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .img-preview {
            display: block;
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }
        .container {
            text-align: center;
        }
        .upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
        }
        .image-preview {
            width: 150px;
            height: 150px;
            background-color: #f0f0f0;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 10px;
            position: relative;
            cursor: pointer;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview::after {
            content: 'Change Image';
            color: white;
            font-size: 14px;
            text-align: center;
            opacity: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        .image-preview:hover img {
            filter: blur(4px);
        }
        .image-preview:hover::after {
            opacity: 1;
        }
        #imageUpload {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="user.css">
    <script>
        function previewImage(event) {
            const output = document.getElementById('imagePreview');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</head>
<body>
<div class="sidebar">
        <h2 style="color:white;">Admin Panel</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="articles.php"><i class="fas fa-newspaper"></i> Articles</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li style="margin-left: 30px;"><a href="add_user.php" class="active"><i class="fas fa-user-plus"></i> Add User</a></li>
            <li style="margin-left: 30px;"><a href="user_view.php"><i class="fas fa-eye"></i> View User</a></li>
            <li style="margin-left: 30px;"><a href="user_edit.php"><i class="fas fa-user-edit"></i> Edit User</a></li>
            <li style="margin-left: 30px;"><a href="user_delete.php"><i class="fas fa-trash-alt"></i> Delete User</a></li>
            <li><a href="settings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <form class="form bg-dark text-white p-4 rounded" action="add_user.php" method="post" enctype="multipart/form-data">
        <h2 class="title text-center mb-3" style="color:white">Add User</h2>
        <p class="message text-center mb-4">Signup now and get Latest News.</p>
        
        <div class="form-group upload-container">
            <div class="image-preview" id="imagePreview" onclick="document.getElementById('profile_image').click();">
                <img src="../User/Profile-PNG-Images.png" alt="Default Image">
            </div>
            <input type="file" name="profile_image" id="profile_image" accept=".png, .jpg, .jpeg" onchange="previewImage(event)" style="display:none;">
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
            </div>
            <div class="form-group col-md-6">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm-password" placeholder="Confirm Password" required>
        </div>

        <button type="submit" class="btn btn-info btn-block submit">Add User</button>
        <p class="signin text-center mt-3">Already have an account? <a href="login.html" class="text-info">Signin</a></p>
    </form>
</div>

<script>
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Image Preview">`;
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = `<img src="default-avatar.png" alt="Default Image">`;
        }
    }
</script>

</body>
</html>
