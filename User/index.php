<?php
session_start();
require_once "config.php";

$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";

// Pagination logic
$limit = 10; // Number of articles per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for the SQL query

$result = null;
$totalPages = 0;

if ($categoryFilter) {
    // Count total articles in the filtered category
    $sql = "SELECT COUNT(*) FROM articles WHERE category = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $categoryFilter);
    $stmt->execute();
    $stmt->bind_result($totalArticles);
    $stmt->fetch();
    $stmt->close();

    // Query with LIMIT and OFFSET for pagination
    $sql = "SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("sii", $categoryFilter, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Count total articles
    $sql = "SELECT COUNT(*) FROM articles";
    $resultTotal = mysqli_query($link, $sql);
    $totalArticles = mysqli_fetch_array($resultTotal)[0];

    // Query with LIMIT and OFFSET for pagination
    $sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($link, $sql);
}

// Calculate total pages
if ($totalArticles > 0) {
    $totalPages = ceil($totalArticles / $limit);
}

if ($result) {
    $numRows = mysqli_num_rows($result);
}

$activeCategory = empty($categoryFilter) ? 'Top NEWS' : $categoryFilter;

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
        .sidebar {
            width: 250px;
            float: left;
            border-right: 1px solid #ddd;
            height: 100vh;
            position: fixed;
            background-color: #f8f9fa;
            padding-top: 20px;
        }

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
            margin-left: -100px;
        }

        /* Result Message Styling */
        .result-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
            margin-left: 400px;
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

.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: #343a40;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

        .sidebar ul li a:hover{
            text-decoration: none;
            background-color: #bbbcbd;
        }

        .article-container a:hover{
            text-decoration: none;
        }

        .pagination {
            justify-content: center;
        }
        .result-message {
            color: #555;
            margin-bottom: 20px;
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle" style="color: white;"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                        <i class="fas fa-phone-alt" style="color: white;"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="subscription.php">
                        <i class="fas fa-crown" style="color: white;"></i> Subscribe
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
                                <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> <?php echo !empty($firstName) ? htmlspecialchars($firstName) : "Profile"; ?></a>
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

    <div class="sidebar bg-light p-3">
        <h5>Categories</h5>
        <ul class="list-unstyled">
            <li class="mb-2"><a href="?category" class="category-link"><i class="fas fa-fire-alt" style="color: #f5b236;"></i> Top NEWS</a></li>
            <li class="mb-2"><a href="?category=Entertainment" class="category-link <?php echo $activeCategory == 'Entertainment' ? 'active' : ''; ?>"><i class="fas fa-film" style="color: #d11515;"></i> Entertainment</a></li>
            <li class="mb-2"><a href="my_city.php" class="category-link <?php echo $activeCategory == 'Local News' ? 'active' : ''; ?>"><i class="fas fa-map-marker-alt" style="color: #4a94e8;"></i> My City</a></li>
            <li class="mb-2"><a href="?category=Business" class="category-link <?php echo $activeCategory == 'Business' ? 'active' : ''; ?>"><i class="fas fa-briefcase" style="color: #27e330;"></i> Business</a></li>
            <li class="mb-2"><a href="?category=World News" class="category-link <?php echo $activeCategory == 'World News' ? 'active' : ''; ?>"><i class="fas fa-globe" style="color: #1444c9;"></i> World News</a></li>
            <li class="mb-2"><a href="?category=Science Journalism" class="category-link <?php echo $activeCategory == 'Science Journalism' ? 'active' : ''; ?>"><i class="fas fa-flask text-secondary"></i> Science Journalism</a></li>
            <li class="mb-2"><a href="?category=Lifestyle Journalism" class="category-link <?php echo $activeCategory == 'Lifestyle Journalism' ? 'active' : ''; ?>"><i class="fas fa-heart" style="color: #e32dbf;"></i> Lifestyle Journalism</a></li>
            <li class="mb-2"><a href="?category=Political Journalism" class="category-link <?php echo $activeCategory == 'Political Journalism' ? 'active' : ''; ?>"><i class="fas fa-landmark text-dark"></i> Political Journalism</a></li>
            <li class="mb-2"><a href="?category=Sports" class="category-link <?php echo $activeCategory == 'Sports' ? 'active' : ''; ?>"><i class="fas fa-football-ball" style="color: orange;"></i> Sports</a></li>
            <li class="mb-2"><a href="?category=Art News" class="category-link <?php echo $activeCategory == 'Art News' ? 'active' : ''; ?>"><i class="fas fa-palette" style="color: purple;"></i> Art News</a></li>
            <li class="mb-2"><a href="?category=Press Releases" class="category-link <?php echo $activeCategory == 'Press Releases' ? 'active' : ''; ?>"><i class="fas fa-bullhorn text-muted"></i> Press Releases</a></li>
        </ul>
    </div>

    <div class="main-content mt-4">
        <?php if ($categoryFilter): ?>
            <div class="result-message">
                <?php if ($numRows > 0): ?>
                    <?php echo $numRows . " result(s) found for category: " . htmlspecialchars($categoryFilter); ?>
                <?php else: ?>
                    Ohh sorry to say, No article related to <?php echo htmlspecialchars($categoryFilter); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="article-container">
                <a href="show_article.php?id=<?php echo $row['id']; ?>">
                    <h3 style="color:black;"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <?php if (!empty($row['cover_image'])): ?>
                <div class="article-media">
                    <img src="../Admin/uploads/<?php echo htmlspecialchars($row['cover_image']); ?>" alt="Cover Image" onerror="this.onerror=null; this.src='fallback_image.jpg';">
                </div>
            <?php endif; ?>
                    <p>Published on: <?php echo htmlspecialchars($row['created_at']); ?></p>
                </a>
                <hr>
                <div style="text-align: right;">
                    <button class="btn btn-sm btn-outline-primary copy-link" data-link="localhost/News Paper/user/show_article.php?id=<?php echo $row['id']; ?>">Copy Link</button>
                </div>
            </div>
        <?php endwhile; ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.querySelectorAll('.copy-link').forEach(function(button) {
        button.addEventListener('click', function() {
            var articleLink = this.getAttribute('data-link');
            
            navigator.clipboard.writeText(articleLink).then(() => {
                const originalText = this.textContent;
                
                this.textContent = "Copied!";
                
                setTimeout(() => {
                    this.textContent = originalText;
                }, 3000);
            }).catch(function(error) {
                console.error("Failed to copy text: ", error);
            });
        });
    });
</script>


</body>
</html>
