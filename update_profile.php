<?php
session_start();
include 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    $_SESSION['update_error'] = "You need to be logged in to update your profile.";
    header("Location: account.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$updateType = isset($_POST['update_type']) ? $_POST['update_type'] : '';

if ($updateType === 'change_password') {
    // Get user input
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Ensure all fields are filled
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['update_error'] = "All fields are required.";
        header("Location: account.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['update_error'] = "New passwords do not match.";
        header("Location: account.php");
        exit();
    }

    // Retrieve current password hash from the database
    $stmt = $connection->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($storedPasswordHash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($currentPassword, $storedPasswordHash)) {
        $_SESSION['update_error'] = "Current password is incorrect.";
        header("Location: account.php");
        exit();
    }

    // Hash new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $newPasswordHash, $userEmail);
    
    if ($stmt->execute()) {
        $_SESSION['update_success'] = "Password changed successfully!";
    } else {
        $_SESSION['update_error'] = "Failed to change password.";
    }
    $stmt->close();
}

// ✅ Handle Profile Picture Upload
if (!empty($_FILES['profilePic']['name'])) {
    $targetDir = "images/profilePics/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = preg_replace("/[^a-zA-Z0-9]/", "_", $_SESSION['user_email']) . ".png";
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFile)) {
        $_SESSION['profile_pic'] = $targetFile;
        $_SESSION['update_success'] = "Profile picture updated successfully!";
    } else {
        $_SESSION['update_error'] = "Error uploading profile picture.";
    }
}

// Redirect back to account page
header("Location: account.php");
exit();
?>
