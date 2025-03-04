<?php
session_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

// Database connection
require_once "config.php";

// Initialize category filter and selected city
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";
$selectedCity = isset($_GET['city']) ? $_GET['city'] : "";

// Fetch articles based on the selected category
$cities = [];
$articlesByCity = [];

// Fetch articles based on the category filter
if ($categoryFilter) {
    $sql = "SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $categoryFilter);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row['city'];
        if (!isset($articlesByCity[$row['city']])) {
            $articlesByCity[$row['city']] = [];
        }
        $articlesByCity[$row['city']][] = $row;
    }
    $stmt->close();
} else {
    $sql = "SELECT * FROM articles ORDER BY created_at DESC";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = $row['city'];
        if (!isset($articlesByCity[$row['city']])) {
            $articlesByCity[$row['city']] = [];
        }
        $articlesByCity[$row['city']][] = $row;
    }
}

// Remove duplicate cities
$cities = array_unique($cities);

// Filter articles by selected city
$articlesForSelectedCity = [];
if (!empty($selectedCity)) {
    if (isset($articlesByCity[$selectedCity])) {
        $articlesForSelectedCity = $articlesByCity[$selectedCity];
    }
}

$activeCategory = empty($categoryFilter) ? 'Top NEWS' : $categoryFilter;
$articlesFound = count($articlesForSelectedCity);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My City - Online Newspaper</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            float: left;
            border-right: 1px solid #ddd;
            height: 100vh;
            position: fixed;
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        /* Category Link Styling */
        .category-link {
            color: #333;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .category-link.active {
            background-color: #495057;
            color: #ffc107;
            font-weight: bold;
        }

        /* Main Content Styling */
        .main-content {
            padding: 20px;
            margin-left: 0px; /* Adjusted for the sidebar */
        }

        /* Result Message Styling */
        .result-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Article Container Styling */
        .article-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto 40px;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .article-container h3 {
            font-size: 28px;
        }

        .article-container p {
            font-size: 18px;
        }

        .article-media img {
            max-width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-right: none;
                margin-bottom: 20px;
            }

            .main-content {
                margin-left: 0;
            }
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

        .sidebar ul li a:hover {
            text-decoration: none;
            background-color: #bbbcbd;
        }

        .search-container {
            display: flex;
            justify-content: <?php echo $selectedCity ? 'flex-end' : 'center'; ?>;
            margin-top: 30px;
        }

        .article-count {
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                                $profileImage = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'profile.png';
                                $profileImagePath = "../User/uploads/" . htmlspecialchars($profileImage);
                            ?>
                            <img src="<?php echo $profileImagePath; ?>" alt="Profile Icon" style="width: 24px; height: 24px;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <?php if ($loggedIn): ?>
                                <a class="dropdown-item" href="profile.php"><?php echo htmlspecialchars($firstName) ?: "Profile"; ?></a>
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

    <!-- Sidebar -->
    <div class="sidebar bg-light p-3">
        <h5>Categories</h5>
        <ul class="list-unstyled">
            <li class="mb-2"><a href="index.php?category" class="category-link"><i class="fas fa-fire-alt" style="color: #f5b236;"></i> Top NEWS</a></li>
            <li class="mb-2"><a href="index.php?category=Entertainment" class="category-link <?php echo $activeCategory == 'Entertainment' ? 'active' : ''; ?>"><i class="fas fa-film" style="color: #d11515;"></i> Entertainment</a></li>
            <li class="mb-2"><a href="my_city.php" class="category-link <?php echo $activeCategory == 'Local News' ? 'active' : ''; ?>"><i class="fas fa-map-marker-alt" style="color: #4a94e8;"></i> My City</a></li>
            <li class="mb-2"><a href="index.php?category=Business" class="category-link <?php echo $activeCategory == 'Business' ? 'active' : ''; ?>"><i class="fas fa-briefcase" style="color: #27e330;"></i> Business</a></li>
            <li class="mb-2"><a href="index.php?category=World News" class="category-link <?php echo $activeCategory == 'World News' ? 'active' : ''; ?>"><i class="fas fa-globe" style="color: #1444c9;"></i> World News</a></li>
            <li class="mb-2"><a href="index.php?category=Science Journalism" class="category-link <?php echo $activeCategory == 'Science Journalism' ? 'active' : ''; ?>"><i class="fas fa-flask text-secondary"></i> Science Journalism</a></li>
            <li class="mb-2"><a href="index.php?category=Lifestyle Journalism" class="category-link <?php echo $activeCategory == 'Lifestyle Journalism' ? 'active' : ''; ?>"><i class="fas fa-heart" style="color: #e32dbf;"></i> Lifestyle Journalism</a></li>
            <li class="mb-2"><a href="index.php?category=Political Journalism" class="category-link <?php echo $activeCategory == 'Political Journalism' ? 'active' : ''; ?>"><i class="fas fa-landmark text-dark"></i> Political Journalism</a></li>
            <li class="mb-2"><a href="index.php?category=Sports" class="category-link <?php echo $activeCategory == 'Sports' ? 'active' : ''; ?>"><i class="fas fa-football-ball" style="color: orange;"></i> Sports</a></li>
            <li class="mb-2"><a href="index.php?category=Art News" class="category-link <?php echo $activeCategory == 'Art News' ? 'active' : ''; ?>"><i class="fas fa-palette" style="color: purple;"></i> Art News</a></li>
            <li class="mb-2"><a href="index.php?category=Press Releases" class="category-link <?php echo $activeCategory == 'Press Releases' ? 'active' : ''; ?>"><i class="fas fa-bullhorn text-muted"></i> Press Releases</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content mt-4">
        <div class="search-container">
            <form action="my_city.php" method="GET">
                <select name="city" class="form-control" style="width: 300px;" required>
                    <option value="">Select a city</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city ?? ''); ?>" <?php echo $selectedCity === $city ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($city ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary ml-2">Search</button>
            </form>
        </div>

        <div class="main-content mt-4">
            <?php
            if ($selectedCity) {
                if ($articlesFound > 0) {
                    echo "<div class='article-count'>$articlesFound article(s) found for city: " . htmlspecialchars($selectedCity) . ".</div>";
                    foreach ($articlesForSelectedCity as $article) {
                        echo "<div class='article-container'>";

                        echo "<h3>" . htmlspecialchars($article['title']) . "</h3>";
                        if (!empty($article['cover_image'])) {
                            $coverImagePath = "../Admin/uploads/" . htmlspecialchars($article['cover_image']);
                            echo "<div class='article-media'>
                                    <img src='$coverImagePath' alt='Cover Image'>
                                  </div>";
                        }
                              echo "<p style='padding-top: 10px;'>" . htmlspecialchars(substr($article['content'], 0, 200)) . "...</p>
                              <p>Published on: " . htmlspecialchars($article['created_at']) . "</p>
                              <a href='show_article.php?id=" . htmlspecialchars($article['id']) . "' class='btn btn-primary'>Read More</a>
                              </div>";
                    }
                } else {
                    echo "<div class='article-count'>No articles found for city: " . htmlspecialchars($selectedCity) . ".</div>";
                }
            } else {
                echo "<div class='article-count'>Please select a city to view articles.</div>";
            }
            ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
