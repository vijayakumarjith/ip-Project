<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header("location: index.php");
    exit;
}

// Database connection parameters
$servername = "localhost"; 
$username_db = "root";         
$password_db = "";            
$dbname = "citeria";      

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $password = "";
$username_err = $password_err = $err = "";

// If request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if username and password are empty
    if (empty(trim($_POST['username'])) || empty(trim($_POST['password']))) {
        $err = "Please enter your username and password.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    }

    // If there are no errors
    if (empty($err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;

        // Try to execute the statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            // Check if username exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    // Verify the password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, allow user login
                        session_start();
                        $_SESSION["username"] = $username;
                        $_SESSION["id"] = $id;
                        $_SESSION["loggedin"] = true;

                        // Redirect user to index page
                        header("location: index.php");
                        exit; // Always exit after redirect
                    } else {
                        $err = "Wrong username or password. Please try again.";
                    }
                }
            } else {
                $err = "Wrong username or password. Please try again.";
            }
        } else {
            $err = "Database query failed. Please try again later.";
        }
    }
    
    // Close the statement and connection
    mysqli_stmt_close($stmt);
}

// Close the connection
mysqli_close($conn);

// Display error message if any
if (!empty($err)) {
    echo "<div class='error-message'>$err</div>";
}
?>
