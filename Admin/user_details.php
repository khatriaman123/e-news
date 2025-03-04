<?php
include 'config.php'; // Database connection

// Get the filter from the URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : '';

// Fetch users based on the filter
if ($filter == 'new' && !empty($date_start) && !empty($date_end)) {
    $userQuery = "SELECT id, first_name, last_name, email, created_at FROM users 
                  WHERE created_at BETWEEN '$date_start' AND '$date_end'
                  ORDER BY created_at DESC";
} else {
    $userQuery = "SELECT id, first_name, last_name, email, created_at FROM users 
                  ORDER BY created_at DESC";
}

$userResult = mysqli_query($link, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container{
            margin-left: 150px;
            margin-top: 50px;
        }
        #suggestions{
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            display: none;
            position: absolute;
        }
        #suggestions li {
            padding: 10px;
            cursor: pointer;
        }
        #suggestions li:hover {
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>User Details</h1>
    
    <!-- Search bar for the user -->
    <input type="text" id="search" class="form-control" placeholder="Search user by name">
    <ul id="suggestions"></ul> <!-- List for auto-suggestions -->

    <table class="table table-striped table-bordered mt-5">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Date Joined</th>
            </tr>
        </thead>
        <tbody id="user-data"> <!-- Table body to display user data -->
    <?php while($row = mysqli_fetch_assoc($userResult)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['first_name']; ?></td>
            <td><?php echo $row['last_name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo date('F j, Y', strtotime($row['created_at'])); ?></td>
            <td>
                <a href="more_user_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info">Show More</a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    // Search keyup event
    $('#search').on('keyup', function(){
        let query = $(this).val();
        
        if (query.length > 0) {
            $.ajax({
                url: 'search_user.php',
                method: 'POST',
                data: { query: query },
                success: function(data){
                    $('#suggestions').fadeIn().html(data); // Show suggestions
                }
            });
        } else {
            $('#suggestions').fadeOut();
        }
    });

    // When a suggestion is clicked
    $(document).on('click', '.suggestion-item', function(){
        let selectedName = $(this).text(); // Get the clicked name
        $('#search').val(selectedName); // Set it in the search box
        $('#suggestions').fadeOut(); // Hide suggestions

        // Fetch the specific user's data
        $.ajax({
            url: 'fetch_user_data.php', // URL to fetch the specific user data
            method: 'POST',
            data: { search: selectedName }, // Send the selected name
            success: function(data){
                $('#user-data').html(data); // Replace the table body with the selected user's data
            }
        });
    });
});
</script>

</body>
</html>

