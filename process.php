<?php

$servername = "localhost"; 
$username = "root";         
$password = "";            
$dbname = "citeria";      


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $issue_type = $conn->real_escape_string($_POST['company']);
    $customer_check = $conn->real_escape_string($_POST['customercheck']);
    $message = $conn->real_escape_string($_POST['msg']);
    

    $sql = "INSERT INTO contact_form (fullname, email, issue_type, customer_check, message)
            VALUES ('$fullname', '$email', '$issue_type', '$customer_check', '$message')";
    

    if ($conn->query($sql) === TRUE) {
        echo "Form submitted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    

    $conn->close();
}
?>
