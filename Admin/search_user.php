<?php
include 'config.php'; // Database connection

if (isset($_POST['query'])) {
    $search = mysqli_real_escape_string($link, $_POST['query']);
    
    $query = "SELECT CONCAT(first_name, ' ', last_name) AS full_name 
              FROM users 
              WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%'
              LIMIT 5"; // Limit the results for suggestions

    $result = mysqli_query($link, $query);
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li class="suggestion-item">' . $row['full_name'] . '</li>';
        }
    } else {
        echo '<li>No suggestions</li>';
    }
}
?>
