<?php

session_start();
require_once "config.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
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
    <title>Contact Us - Your Online Newspaper</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
        }
        .contact-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            text-align: center;
        }
        .contact-item {
            margin-bottom: 25px;
        }
        .contact-item strong {
            display: block;
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }
        .contact-item a {
            color: #1a73e8;
            text-decoration: none;
        }
        .contact-item a:hover {
            text-decoration: underline;
        }
        .social-icons {
            text-align: center;
            margin-top: 30px;
        }
        .social-icons a {
            margin: 0 10px;
            color: #555;
            font-size: 24px;
            transition: color 0.3s;
        }
        .social-icons a:hover {
            color: #1a73e8;
            text-decoration: none;
        }
        .social-icons a.fa-whatsapp:hover {
            color: #25d366;
        }
        .social-icons a.fa-facebook:hover {
            color: #4267B2;
        }
        .social-icons a.fa-twitter:hover {
            color: #1DA1F2;
        }
        .social-icons a.fa-instagram:hover {
            color: #E1306C;
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
                        <a class="nav-link active" href="contact.php">
                            <i class="fas fa-phone-alt" style="color: white;"></i> Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo $profileImagePath; ?>" alt="Profile Icon" style="width: 24px; height: 24px; border-radius: 50%;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <?php if ($loggedIn): ?>
                                <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> <?php echo !empty($firstName) ? htmlspecialchars($firstName) : "Profile"; ?></a>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            <?php else: ?>
                                <a class="dropdown-item" href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                                <a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="contact-container">
    <h1>Contact Us</h1>
    <p>We value your feedback and are here to help with any inquiries, suggestions, or concerns.</p>
    
    <div class="contact-item">
        <strong>General Inquiries</strong>
        Email: <a href="mailto:khatriaman2021@gmail.com?subject=General Inquiry&body=Hello,%0D%0A%0D%0AI would like to inquire about...%0D%0A%0D%0AThank you,%0D%0AYour Name">khatriaman2021@gmail.com</a><br>
        Phone: +91 9825337484
    </div>

    <div class="contact-item">
        <strong>News Tips & Story Submissions</strong>
        Email: <a href="mailto:khatriaman2024@gmail.com?subject=Story Submission&body=Hello,%0D%0A%0D%0AI have a story idea I would like to submit...%0D%0A%0D%0AThank you,%0D%0AYour Name">khatriaman2024@gmail.com</a>
    </div>

    <div class="contact-item">
        <strong>Advertising</strong>
        Email: <a href="mailto:khatriaman2911@gmail.com?subject=Advertising Inquiry&body=Hello,%0D%0A%0D%0AI am interested in advertising...%0D%0A%0D%0AThank you,%0D%0AYour Name">khatriaman2911@gmail.com</a><br>
        Phone: +91 9825337484
    </div>

    <div class="contact-item">
        <strong>Follow Us on Social Media</strong>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=100081559892746" class="fab fa-facebook" aria-label="Facebook" target="_blank"></a>
            <a href="https://x.com/KhatriAman28081" class="fab fa-twitter" aria-label="Twitter" target="_blank"></a>
            <a href="https://www.instagram.com/khatriaman123/" class="fab fa-instagram" aria-label="Instagram" target="_blank"></a>
            <a href="https://wa.me/9825337484" class="fab fa-whatsapp" aria-label="WhatsApp" target="_blank"></a>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
