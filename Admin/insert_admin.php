<?php
// Include config file
require_once "config.php";

// Define the username and password
$username = "aman";
$password = "khatri@123";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare an insert statement
$sql = "INSERT INTO admins (username, password1) VALUES (?, ?)";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
    
    // Set parameters
    $param_username = $username;
    $param_password = $hashed_password;
    
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        echo "User inserted successfully.";
    } else{
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else{
    echo "ERROR: Could not prepare query: $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>
