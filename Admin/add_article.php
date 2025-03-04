<?php
session_start();

// Check if the user is logged in, if not redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "config.php"; // Include database connection file

    // Collect form inputs
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $city = !empty($_POST['city']) ? $_POST['city'] : null; // City is optional
    $cover_image = null;
    $media = null;

    // Handle cover image upload
    if (!empty($_FILES['cover_image']['name'])) {
        $cover_image = $_FILES['cover_image']['name'];
        $target_dir = "../admin/uploads/";
        $target_file = $target_dir . basename($_FILES["cover_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["cover_image"]["size"] > 5000000) {
            echo "Sorry, your cover image file is too large.";
            exit;
        }

        $allowed_formats = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed for the cover image.";
            exit;
        }

        if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your cover image.";
            exit;
        }
    }

    // Handle media file upload
    if (!empty($_FILES['media']['name'])) {
        $media = $_FILES['media']['name'];
        $target_dir = "../admin/uploads/";
        $target_file = $target_dir . basename($_FILES["media"]["name"]);
        $mediaFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["media"]["size"] > 10000000) { // Allow up to 10MB for media
            echo "Sorry, your media file is too large.";
            exit;
        }

        $allowed_formats = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'pdf'];
        if (!in_array($mediaFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, MP4, MP3, and PDF files are allowed for media.";
            exit;
        }

        if (!move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your media file.";
            exit;
        }
    }

    // Insert article data into the database
    $sql = "INSERT INTO articles (title, content, cover_image, media, category, city, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssss", $param_title, $param_content, $param_cover_image, $param_media, $param_category, $param_city);
        
        // Set parameters
        $param_title = $title;
        $param_content = $content;
        $param_cover_image = $cover_image;
        $param_media = $media;
        $param_category = $category;
        $param_city = $city;

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            header("location: articles.php");
            exit;
        } else {
            echo "Error: Could not execute query. Please try again later.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare query. Please try again later.";
    }

    mysqli_close($link); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Submit a New Article</h2>
        <form action="add_article.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Article Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter article title" required>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" placeholder="Enter article content" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="" disabled selected>Select category</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Business">Business</option>
                    <option value="World News">World News</option>
                    <option value="Science Journalism">Science Journalism</option>
                    <option value="Lifestyle Journalism">Lifestyle Journalism</option>
                    <option value="Political Journalism">Political Journalism</option>
                    <option value="Sports">Sports</option>
                    <option value="Art News">Art News</option>
                    <option value="Press Releases">Press Releases</option>
                    <option value="Entertainment">Entertainment</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="city" class="form-label">City (Optional)</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city name (optional)">
            </div>
            
            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image</label>
                <input class="form-control" type="file" id="cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.gif">
                <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 5MB.</small>
            </div>

            <div class="mb-3">
                <label for="media" class="form-label">Additional Media</label>
                <input class="form-control" type="file" id="media" name="media" accept=".jpg,.jpeg,.png,.gif,.mp4,.mp3,.pdf">
                <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF, MP4, MP3, PDF. Max size: 10MB.</small>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit Article</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
