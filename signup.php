<?php
$servername = "localhost"; 
$username_db = "root";         
$password_db = "";            
$dbname = "citeria";      

// Create database connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST['username']))) {
        $username_err = "Username cannot be blank.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set the value of param_username
            $param_username = trim($_POST['username']);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST['username']);
                }
            } else {
                echo "Something went wrong.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Check for password
    if (empty(trim($_POST['password']))) {
        $password_err = "Password cannot be blank.";
    } elseif (strlen(trim($_POST['password'])) < 5) {
        $password_err = "Password must be at least 5 characters.";
    } else {
        $password = trim($_POST['password']);
    }

    // Check for confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = "Please confirm your password.";
    } elseif (trim($_POST['password']) != trim($_POST['confirm_password'])) {
        $confirm_password_err = "Password and confirm password do not match.";
    } else {
        $confirm_password = trim($_POST['confirm_password']);
    }

    // If there are no errors, insert into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set these parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            // Try to execute the query
            if (mysqli_stmt_execute($stmt)) {
                header("location: login.html");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Close the connection
    mysqli_close($conn);
}
?>
