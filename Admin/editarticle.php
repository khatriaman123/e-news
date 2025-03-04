<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];

    $sql = "SELECT * FROM articles WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $article_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $article = mysqli_fetch_assoc($result);
            } else {
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $city = $_POST['city']; 

$cover_image = $article['cover_image'];
if (!empty($_FILES['cover_image']['name'])) {
    $cover_target_dir = "uploads/";
    $cover_target_file = $cover_target_dir . basename($_FILES['cover_image']['name']);
    $cover_file_type = strtolower(pathinfo($cover_target_file, PATHINFO_EXTENSION));

    if (in_array($cover_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_target_file)) {
            $cover_image = basename($_FILES['cover_image']['name']);
        } else {
            echo "Sorry, there was an error uploading your cover image.";
        }
    } else {
        echo "Invalid file format for the cover image.";
    }
}

    $media_files = $article['media'] ? json_decode($article['media'], true) : [];
    if (!empty($_FILES['media']['name'][0])) {
        $target_dir = "uploads/";
        foreach ($_FILES['media']['name'] as $key => $file_name) {
            $target_file = $target_dir . basename($file_name);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (in_array($file_type, ['jpg', 'png', 'jpeg', 'gif', 'mp4', 'avi', 'mov'])) {
                if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $target_file)) {
                    $media_files[] = $file_name;
                }
            }
        }
    }

    if (isset($_POST['delete_media'])) {
        foreach ($_POST['delete_media'] as $delete_file) {
            $key = array_search($delete_file, $media_files);
            if ($key !== false) {
                unset($media_files[$key]);
                unlink("uploads/" . $delete_file);
            }
        }
        $media_files = array_values($media_files);
    }

    $sql = "UPDATE articles SET title = ?, content = ?, category = ?, city = ?, media = ?, cover_image = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        $media_json = json_encode($media_files);
        mysqli_stmt_bind_param($stmt, "ssssssi", $param_title, $param_content, $param_category, $param_city, $media_json, $cover_image, $param_id);
        $param_title = $title;
        $param_content = $content;
        $param_category = $category;
        $param_city = $city; 
        $param_id = $article_id;

        if (mysqli_stmt_execute($stmt)) {
            header("location: articles.php");
            exit;
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Article</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $article_id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $article['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" class="form-control" rows="5" required><?php echo $article['content']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" class="form-control">
                    <option value="Entertainment" <?php if ($article['category'] == 'Entertainment') echo 'selected'; ?>>Entertainment</option>
                    <option value="Local News" <?php if ($article['category'] == 'Local News') echo 'selected'; ?>>Local News</option>
                    <option value="Business" <?php if ($article['category'] == 'Business') echo 'selected'; ?>>Business</option>
                    <option value="World News" <?php if ($article['category'] == 'World News') echo 'selected'; ?>>World News</option>
                    <option value="Science Journalism" <?php if ($article['category'] == 'Science Journalism') echo 'selected'; ?>>Science Journalism</option>
                    <option value="Lifestyle Journalism" <?php if ($article['category'] == 'Lifestyle Journalism') echo 'selected'; ?>>Lifestyle Journalism</option>
                    <option value="Political Journalism" <?php if ($article['category'] == 'Political Journalism') echo 'selected'; ?>>Political Journalism</option>
                    <option value="Sports" <?php if ($article['category'] == 'Sports') echo 'selected'; ?>>Sports</option>
                    <option value="Art News" <?php if ($article['category'] == 'Art News') echo 'selected'; ?>>Art News</option>
                    <option value="Press Releases" <?php if ($article['category'] == 'Press Releases') echo 'selected'; ?>>Press Releases</option>
                </select>
            </div>
            <div class="form-group">
                <label for="city">City (Gujarat)</label>
                <select name="city" id="city" class="form-control" required>
                    <option value="Ahmedabad" <?php if ($article['city'] == 'Ahmedabad') echo 'selected'; ?>>Ahmedabad</option>
                    <option value="Surat" <?php if ($article['city'] == 'Surat') echo 'selected'; ?>>Surat</option>
                    <option value="Vadodara" <?php if ($article['city'] == 'Vadodara') echo 'selected'; ?>>Vadodara</option>
                    <option value="Rajkot" <?php if ($article['city'] == 'Rajkot') echo 'selected'; ?>>Rajkot</option>
                    <option value="Bhavnagar" <?php if ($article['city'] == 'Bhavnagar') echo 'selected'; ?>>Bhavnagar</option>
                    <option value="Jamnagar" <?php if ($article['city'] == 'Jamnagar') echo 'selected'; ?>>Jamnagar</option>
                    <option value="Junagadh" <?php if ($article['city'] == 'Junagadh') echo 'selected'; ?>>Junagadh</option>
                    <option value="Gandhinagar" <?php if ($article['city'] == 'Gandhinagar') echo 'selected'; ?>>Gandhinagar</option>
                    <option value="Anand" <?php if ($article['city'] == 'Anand') echo 'selected'; ?>>Anand</option>
                    <option value="Nadiad" <?php if ($article['city'] == 'Nadiad') echo 'selected'; ?>>Nadiad</option>
                    <option value="Morbi" <?php if ($article['city'] == 'Morbi') echo 'selected'; ?>>Morbi</option>
                    <option value="Mehsana" <?php if ($article['city'] == 'Mehsana') echo 'selected'; ?>>Mehsana</option>
                    <option value="Bhuj" <?php if ($article['city'] == 'Bhuj') echo 'selected'; ?>>Bhuj</option>
                    <option value="Porbandar" <?php if ($article['city'] == 'Porbandar') echo 'selected'; ?>>Porbandar</option>
                    <option value="Godhra" <?php if ($article['city'] == 'Godhra') echo 'selected'; ?>>Godhra</option>
                    <option value="Patan" <?php if ($article['city'] == 'Patan') echo 'selected'; ?>>Patan</option>
                    <option value="Navsari" <?php if ($article['city'] == 'Navsari') echo 'selected'; ?>>Navsari</option>
                    <option value="Veraval" <?php if ($article['city'] == 'Veraval') echo 'selected'; ?>>Veraval</option>
                    <option value="Dahod" <?php if ($article['city'] == 'Dahod') echo 'selected'; ?>>Dahod</option>
                    <option value="Bharuch" <?php if ($article['city'] == 'Bharuch') echo 'selected'; ?>>Bharuch</option>
                    <option value="Valsad" <?php if ($article['city'] == 'Valsad') echo 'selected'; ?>>Valsad</option>
                </select>
            </div>
            <div class="form-group">
            <label for="cover_image">Cover Image</label>
                <input type="file" name="cover_image" class="form-control">
                <small class="form-text text-muted">Upload a cover image (jpg, png, jpeg, gif).</small>
                <?php if ($article['cover_image']) { ?>
                    <p>Current Cover Image: <a href="uploads/<?php echo $article['cover_image']; ?>" target="_blank">View</a></p>
                <?php } ?>
                <label for="media">Upload Media</label>
                <input type="file" name="media[]" class="form-control" multiple>
                <small class="form-text text-muted">You can upload multiple files (images or videos).</small>
                <?php
                $media_files = $article['media'] ? json_decode($article['media'], true) : [];
                if ($media_files) {
                    echo '<p>Current Media:</p>';
                    foreach ($media_files as $file) {
                        echo "<div><input type='checkbox' name='delete_media[]' value='$file'> $file <a href='uploads/$file' target='_blank'>View</a></div>";
                    }
                }
                ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Article</button>
            <a href="articles.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
