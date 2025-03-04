<?php
session_start();

$loggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$firstName = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "";
$userId = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

require_once "config.php";
$articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$incrementViewsQuery = "UPDATE articles SET views = views + 1 WHERE id = $articleId";
mysqli_query($link, $incrementViewsQuery);

if ($articleId <= 0) {
    die("Invalid article ID.");
}

$sql = "INSERT INTO user_activity (user_id, article_id) VALUES (?, ?)";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param('ii', $userId, $articleId); // Use defined variables
    $stmt->execute();
} else {
    echo "Error: " . $link->error; // Use $link instead of $conn
}



$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $articleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Article not found.");
}

$article = $result->fetch_assoc();
$category = $article['category']; 
$stmt->close();


$sql_related = "SELECT * FROM articles WHERE category = ? AND id != ?";
$stmt_related = $link->prepare($sql_related);
$stmt_related->bind_param("si", $category, $articleId);
$stmt_related->execute();
$related_result = $stmt_related->get_result();
$stmt_related->close();

$articleURL = "localhost/News Paper/user/show_article.php?id=" . $articleId;

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";

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

$activeCategory = empty($categoryFilter) ? 'Top NEWS' : $categoryFilter;

$articleUrl = "C:\wamp64\www\News Paper\User\show_article.php?id=" . $articleId;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Online Newspaper</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .article-content {
            width: 90%;
            margin: 0 auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .article-content img {
            max-width: 100%;
            max-height: 300px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .back-button {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .related-articles {
            margin-top: 40px;
        }
        .related-articles h4 {
            margin-bottom: 20px;
        }
        .related-articles a {
            display: block;
            margin-bottom: 10px;
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
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 70px;
    left: 0;
    background-color: #f8f9fa;
    border-right: 1px solid #ddd;
    padding-top: 20px;
    overflow-y: auto;
}

.sidebar h5 {
    margin-left: 20px;
    color: #333;
}

.sidebar ul {
    list-style-type: none;
    padding-left: 0;
    margin-left: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
}

.sidebar ul li a {
    display: block;
    padding: 10px 20px;
    color: #343a40;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.sidebar ul li a:hover {
    background-color: #e9ecef;
    color: #000;
}

.sidebar ul li a.active {
    background-color: #495057;
    color: #ffc107;
    font-weight: bold;
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
}

.related-articles {
    margin-top: 40px;
    width: 850px;
}

.related-articles h4 {
    margin-bottom: 20px;
}

.related-article-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f8f9fa; 
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); 
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.related-article-item:hover {
    transform: translateY(-2px); 
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15); 
}

.related-article-image {
    width: 100px;
    height: auto;
    border-radius: 5px;
    margin-right: 15px;
}

.related-article-title {
    font-size: 16px;
    font-weight: bold;
}

.related-article-title a {
    text-decoration: none;
    color: #343a40;
    transition: color 0.3s ease;
}

.related-article-title a:hover {
    color: #007bff; 
}
.social-sharing {
    margin: 20px 0;
}
.social-sharing a i {
    color: #555;
    margin-right: 5px; 
    font-size: 50px;
    margin-right: 20px;
    background-color: transparent;
    decoration: none;
}

.social-sharing a:hover {
    text-decoration: none;
}

.social-sharing i.fa-whatsapp:hover {
            color: #25d366;
        }
        .social-sharing i.fa-facebook:hover {
            color: #4267B2;
        }
        .social-sharing i.fa-twitter:hover {
            color: #1DA1F2;
        }
        .social-sharing i.fa-instagram:hover {
            color: #E1306C;
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
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-logout">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                            $profileImage = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'profile.png';
                            $profileImagePath = "../User/uploads/" . htmlspecialchars($profileImage);
                        ?>

                        <img src="<?php echo $profileImagePath; ?>" alt="Profile Icon" style="width: 24px; height: 24px; border-radius: 50%;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                            <?php if ($loggedIn): ?>
                                <a class="dropdown-item" href="profile.php"><?php if ($loggedIn && !empty($firstName)) {echo htmlspecialchars($firstName);} else {echo "Profile";} ?></a>
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

    
    <div class="container mt-4">
        <div class="article-content">
            
            <a class="btn btn-secondary back-button" href="index.php">Back to Articles</a>
            <h1><?php echo htmlspecialchars($article['title']); ?></h1>
            <?php if (!empty($article['cover_image'])): ?>
                <img src="../Admin/uploads/<?php echo htmlspecialchars($article['cover_image']); ?>" alt="Cover Image" class="img-fluid mb-4" style="max-height: 500px; width: 100%; object-fit: cover;">
            <?php endif; ?>

            <?php if (!empty($article['media'])): ?>
                <?php
                $media = json_decode($article['media'], true);
                foreach ($media as $file): ?>
                    <img src="../Admin/uploads/<?php echo htmlspecialchars($file); ?>" alt="Article Image">
                <?php endforeach; ?>
            <?php endif; ?>
            <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
            <p>Published on: <?php echo htmlspecialchars($article['created_at']); ?></p>
            
            <a class="btn btn-secondary back-button" href="index.php"><i class="fas fa-arrow-left"></i> Back to Articles</a>
            <button class="btn btn-sm btn-outline-primary copy-link" data-link="<?php echo $articleURL; ?>">Copy Link</button>
            <div class="social-sharing">
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($articleUrl); ?>" target="_blank">
        <i class="fab fa-facebook"></i>
    </a>
    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($articleUrl); ?>" target="_blank">
        <i class="fab fa-twitter"></i>
    </a>
    <a href="https://www.instagram.com/create/story?media=<?php echo urlencode($articleUrl); ?>" target="_blank">
    <i class="fab fa-instagram"></i>
</a>

    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($articleUrl); ?>" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

        </div>

        
<?php if ($related_result->num_rows > 0): ?>
    <div class="related-articles" style="margin-left: 100px;">
        <h4>Also Read:</h4>
        <?php while ($related_article = $related_result->fetch_assoc()): ?>
            <div class="related-article-item">
                <?php if (!empty($related_article['cover_image'])): ?>
                    <img src="../Admin/uploads/<?php echo htmlspecialchars($related_article['cover_image']); ?>" alt="Related Article Cover" class="related-article-image">
                <?php endif; ?>
                <div class="related-article-title">
                    <a href="show_article.php?id=<?php echo $related_article['id']; ?>">
                        <?php echo htmlspecialchars($related_article['title']); ?>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

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
