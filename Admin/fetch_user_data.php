<?php
include 'config.php'; // Database connection

if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($link, $_POST['search']);
    
    // Query to fetch only the selected user's data
    $query = "SELECT id, first_name, last_name, email, created_at 
              FROM users 
              WHERE CONCAT(first_name, ' ', last_name) = '$search'";
    
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['first_name'] . '</td>
                    <td>' . $row['last_name'] . '</td>
                    <td>' . $row['email'] . '</td>
                    <td>' . date('F j, Y', strtotime($row['created_at'])) . '</td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="5">No user found</td></tr>';
    }
}
?>
