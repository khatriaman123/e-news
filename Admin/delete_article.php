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

    // Prepare the delete statement
    $sql = "DELETE FROM articles WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $article_id;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Article deleted successfully, redirect to the specified page
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'articles.php';
            header("location: $redirect");
            exit;
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Redirect to articles page if the ID is invalid
    header("location: articles.php");
    exit;
}

// Close the database connection
mysqli_close($link);
?>
