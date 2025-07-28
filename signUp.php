<?php
// Start the session
session_start();

// Include the connection file
include 'connection.php';

// Check if the form data is set
if (isset($_POST['email'], $_POST['forename'], $_POST['surname'], $_POST['password'])) {
    // Prepare the SQL statement
    $stmt = $connection->prepare("INSERT INTO USERS (email, firstName, surname, password, admin) VALUES (?, ?, ?, ?, '0')");
    
    // Bind parameters to the prepared statement
    $stmt->bind_param("ssss", $_POST['email'], $_POST['forename'], $_POST['surname'], $hashedPassword);

    // Hash the password before storing it
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Execute the prepared statement
    if ($stmt->execute()) {
        // Store user data in session variables
        $_SESSION['user_email'] = $_POST['email'];
        $_SESSION['user_forename'] = $_POST['forename'];
        $_SESSION['user_surname'] = $_POST['surname'];
        $_SESSION['is_admin'] = '0'; // Since we know this will always be 0 for this form
        
        // Redirect to account page
        header('Location: ./account.php');
        exit();
    } else {
// Attempt to execute the prepared statement
		if (!$stmt->execute()) {
			// Check for duplicate entry
			if (strpos($stmt->error, 'Duplicate entry') !== false) {
				$_SESSION['error'] = "The email address already exists. Please use a different email to register.";
			} else {
				// Generic error message for other errors
				$_SESSION['error'] = "An unexpected error occurred. Please try again.";
			}

			header("Location: ./register.php");
			exit();
}
		
    }
    
    // Close the statement
    $stmt->close();
} else {
    echo "All fields are required.";
}

// Close the database connection
$connection->close();
?>
