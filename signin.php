<?php
session_start();
include 'connection.php';

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if ($email && $password) {
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Prepare statement to prevent SQL injection
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables correctly
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_forename'] = $user['firstName'];  
            $_SESSION['user_surname'] = $user['surname'];  
            $_SESSION['admin'] = $user['admin'];    
            
            // Redirect to the account page
            header("Location: account.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No user found with that email address.";
    }

    $stmt->close();
    $connection->close();
} else {
    $_SESSION['error'] = "Please enter both email and password.";
}

// Redirect to login page if login fails
header("Location: login.php");
exit();
?>
