<?php
session_start();
require_once "config.php";

// Check if the user is logged in
$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

// Initialize category filter
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";

// Fetch articles based on the selected category
$result = null;
$numRows = 0;

if ($categoryFilter) {
    $sql = "SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $categoryFilter);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT * FROM articles ORDER BY created_at DESC";
    $result = mysqli_query($link, $sql);
}

if ($result) {
    $numRows = mysqli_num_rows($result);
}

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
    <title>Home - Online Newspaper</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Add your styles here */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            height: 75px;
            z-index: 1000;
            background-color: #343a40;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        body {
            padding-top: 70px;
        }
        .navbar-brand {
            margin-right: auto;
            margin: 100px;
        }
        .navbar-nav .nav-item {
            text-align: center;
        }
        .navbar-nav .nav-link.active {
            background-color: #495057;
            color: #ffc107;
            border-bottom: 4px solid #ffc107;
        }
        /* Header for Subscription Page */
        header {
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
        }
        header h1 {
            font-size: 36px;
            margin: 0;
        }
        .subscription-options {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .plan {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            width: 30%;
        }
        .plan h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .plan ul {
            list-style: none;
            padding: 0;
        }
        .plan ul li {
            margin-bottom: 5px;
        }
        .subscribe-btn {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        .payment-options {
            margin-top: 30px;
            text-align: center;
        }
        .payment-options form {
            display: inline-block;
        }

        .navbar-logout{
            margin-left: 200px;
        }
        /* Rest of the styles (Sidebar, Articles) remain the same */
    </style>
</head>
<body>
    <!-- Subscription Header -->
    <header>
        <h1>Subscribe to Our Newspaper</h1>
    </header>

    <!-- Sidebar and Main Content remain unchanged -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navigation">
        <div class="container">
            <a class="navbar-brand navbar-logo" style="margin-left: -50px;" href="index.php">
                <img src="default-monochrome-white.png" alt="Logo" height="50px">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-phone-alt"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="subscription.html">
                            <i class="fas fa-crown"></i> Subscribe
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo $profileImagePath; ?>" alt="Profile Icon" style="width: 24px; height: 24px; border-radius: 50%;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <?php if ($loggedIn): ?>
                                <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($firstName); ?></a>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            <?php else: ?>
                                <a class="dropdown-item" href="register.html"><i class="fas fa-user-plus"></i> Register</a>
                                <a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Subscription Plans Section -->
    <section class="subscription-options">
        <div class="plan">
            <h2>Basic Plan</h2>
            <p>Get access to general articles and selected categories.</p>
            <ul>
                <li>Access to general news</li>
                <li>Category: Sports, Politics, and Entertainment</li>
                <li>Access to limited city-based news</li>
            </ul>
            <button class="subscribe-btn">Subscribe - $5/month</button>
        </div>
        <div class="plan">
            <h2>Standard Plan</h2>
            <p>Unlock access to all categories and city-based news search.</p>
            <ul>
                <li>Access to all categories</li>
                <li>Full city news search functionality</li>
                <li>Exclusive newsletters and updates</li>
            </ul>
            <button class="subscribe-btn">Subscribe - $10/month</button>
        </div>
        <div class="plan">
            <h2>Premium Plan</h2>
            <p>All features unlocked with priority customer support.</p>
            <ul>
                <li>All category and city news access</li>
                <li>Ad-free experience</li>
                <li>Priority customer support</li>
            </ul>
            <button class="subscribe-btn">Subscribe - $15/month</button>
        </div>
    </section>

    <!-- Payment Options Section -->
    <section class="payment-options">
        <h2>Choose Payment Method</h2>
        <form action="process_payment.php" method="post">
            <label for="plan">Select Plan:</label>
            <select id="plan" name="plan">
                <option value="basic">Basic - $5/month</option>
                <option value="standard">Standard - $10/month</option>
                <option value="premium">Premium - $15/month</option>
            </select><br><br>
            <button type="submit" class="subscribe-btn">Proceed to Payment</button>
        </form>
    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
