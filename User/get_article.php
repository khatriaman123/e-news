<?php
header('Content-Type: application/json');

require_once "../admin/config.php";

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$category = $category === 'all' ? '%' : $category;

$sql = "SELECT * FROM articles WHERE category LIKE ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $category);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $articles = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($articles);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute query.']);
    }
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare statement.']);
}

mysqli_close($link);
?>
