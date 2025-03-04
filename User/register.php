<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "e_newspaper";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $image = $_FILES['image'];

    // Handle image upload
    $image_name = "default-avatar.png"; // Default image
    if ($image['size'] > 0) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($image["name"]);
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($image["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO users (first_name, last_name, email, password, profile_image) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $image_name);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: login.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
