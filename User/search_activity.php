<?php
require_once 'config.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Adjust the SQL to join the `user_activity`, `articles`, and `users` tables
    $sql = "
        SELECT a.title 
        FROM user_activity ua
        JOIN articles a ON ua.article_id = a.id
        JOIN users u ON ua.user_id = u.id
        WHERE a.title LIKE ? 
        LIMIT 5";

    $stmt = $link->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the suggestions based on the result
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="#" class="list-group-item list-group-item-action suggestion-item">' . htmlspecialchars($row['title']) . '</a>';
        }
    } else {
        echo '<p class="list-group-item">No matches found</p>';
    }
}
?>
