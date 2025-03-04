<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Database connection
require_once "config.php";

// Check if the article ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    // Fetch article data
    $sql = "SELECT * FROM articles WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $article_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $article = mysqli_fetch_assoc($result);
            } else {
                // Redirect to articles page if no article found
                header("location: articles.php");
                exit;
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("location: articles.php");
    exit;
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .article-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .cover-image {
            width: 100%;
            margin-bottom: 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .article-image {
            flex: 0 0 200px;
            margin-right: 20px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .article-content {
            flex: 1;
        }
        .article-content.full-width {
            flex: 0 0 100%;
            margin-top: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .back-button {
            margin-top: 20px;
        }
        .edit-button {
            margin-top: 20px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5"><?php echo htmlspecialchars($article['title'] ?? ''); ?></h1>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category'] ?? ''); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($article['city'] ?? ''); ?></p>
        
        <?php if (!empty($article['cover_image'])): ?>
            <img src="../Admin/uploads/<?php echo htmlspecialchars($article['cover_image']); ?>" alt="Cover Image" class="img-fluid mb-4" style="max-height: auto; width: auto; object-fit: cover;">
        <?php endif; ?>
        
        <div class="article-container">
            <?php
            // Display additional images, excluding the first one (cover image)
            if (!empty($media)) {
                foreach (array_slice($media, 1) as $file) {
                    echo '<div class="article-image"><a href="uploads/' . htmlspecialchars($file) . '" target="_blank"><img src="uploads/' . htmlspecialchars($file) . '" alt="Article Image"></a></div>';
                }
            }
            ?>
            <div class="article-content <?php echo empty($media) ? 'full-width' : ''; ?>">
                <?php echo nl2br(htmlspecialchars($article['content'] ?? '')); ?>
            </div>
        </div>
        <a href="articles.php" class="btn btn-secondary back-button">Back to Articles</a>
        <a href="editarticle.php?id=<?php echo $article_id; ?>" class="btn btn-primary edit-button">Edit Article</a>
    </div>

    <!-- Bootstrap JS and dependencies (jQuery, Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
