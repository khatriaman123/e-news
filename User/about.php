<?php
// Example PHP variables (can be pulled from the database)
$site_name = "Taza Khabar";
$founding_year = 2020;

session_start();
require_once "config.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page with a redirect URL
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Check if the user is logged in
$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

if ($loggedIn && empty($_SESSION['profile_image'])) {
    // Fetch user data from the database
    $sql = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Set the profile image in session
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>About Us | <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Global Styles */

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1, h2, h3 {
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .about-us {
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .about-us h1 {
            font-size: 36px;
            color: #2c3e50;
        }

        .about-us p {
            font-size: 16px;
        }

        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .about-us {
                padding: 20px;
            }

            .about-us h1 {
                font-size: 28px;
            }

            .about-us p {
                font-size: 14px;
            }
        }

        /* Button styling (optional) */
        .feedback-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e74c3c;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .feedback-btn:hover {
            text-decoration: none;
            color: #e74c3c;
            background-color: white;
            border: 1px solid #e74c3c;
        }
        .navbar-nav {
            
            margin-left: auto;
            margin-right: auto;
        }

        /* Fixed Header Styling */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: #343a40; /* Bootstrap's bg-dark color */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    height: 80px;
}

/* Adjust page content to not hide under the fixed header */
body {
    padding-top: 70px; /* Adjust based on the height of your header */
}

/* Navbar Brand Styling */
.navbar-brand {
    margin-right: auto;
}

/* Navbar Item Styling */
.navbar-nav .nav-item {
    text-align: center;
}

/* Navbar Active Link Styling */
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
                        <a class="nav-link active" href="about.php">
                            <i class="fas fa-info-circle" style="color: white;"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-phone-alt" style="color: white;"></i> Contact Us
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <!-- Profile Dropdown -->
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

    <div class="container">
        <div class="about-us">
            <h1>About Us</h1>
            <p>Welcome to <span class="highlight"><?php echo $site_name; ?></span>! We believe in the power of journalism to inform, inspire, and engage. As a dedicated online news platform, our mission is to bring you the most relevant stories, insightful analysis, and in-depth reports on the issues that matter most—from local events to global trends.</p>

            <h2>Our Story</h2>
            <p>Founded in <?php echo $founding_year; ?>, <span class="highlight"><?php echo $site_name; ?></span> was created with a vision to provide a space where news isn’t just reported, but explored with integrity, transparency, and accuracy.</p>

            <h2>What We Cover</h2>
            <p><strong>Breaking News</strong>: Stay up to date with real-time coverage of events as they unfold.</p>
            <p><strong>In-Depth Analysis</strong>: Dive deeper into stories with expert commentary and investigative reports.</p>
            <p><strong>Local Focus</strong>: Our "My City" section keeps you connected with the pulse of your community.</p>
            <p><strong>Global Perspective</strong>: Understand the bigger picture with coverage of international events and trends.</p>

            <h2>Our Values</h2>
            <p><strong>Integrity:</strong> Our reporting is independent, unbiased, and rooted in truth.</p>
            <p><strong>Community:</strong> We are deeply connected to our readers and value your voice.</p>
            <p><strong>Innovation:</strong> We are committed to embracing technology to improve the way we bring you the news.</p>

            <h2>Join the Conversation</h2>
            <p>We invite you to engage with us. Share your opinions, provide feedback, or pitch stories through our various channels. Together, we can make a difference in how news is shared and understood.</p>

            <a href="contact.php" class="feedback-btn">Contact Us</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
