<?php
session_start();
include 'connection.php';
include 'header.php';

// Ensure user is an admin
if (!isset($_SESSION['user_email']) || $_SESSION['admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Get customer email
$customerEmail = isset($_GET['email']) ? $_GET['email'] : null;
if (!$customerEmail) {
    header("Location: manage_customers.php");
    exit();
}

// Fetch customer details
$stmt = $connection->prepare("SELECT firstName, surname, email, admin FROM users WHERE email = ?");
$stmt->bind_param("s", $customerEmail);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    header("Location: manage_customers.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    $newFirstName = trim($_POST['firstName']);
    $newSurname = trim($_POST['surname']);
    $newEmail = trim($_POST['email']);
    $newAdminStatus = isset($_POST['admin']) ? 1 : 0; // Convert checkbox to integer (0 or 1)

    if (!empty($newFirstName) && !empty($newSurname) && !empty($newEmail)) {
        $stmt = $connection->prepare("UPDATE users SET firstName = ?, surname = ?, email = ?, admin = ? WHERE email = ?");
        $stmt->bind_param("sssis", $newFirstName, $newSurname, $newEmail, $newAdminStatus, $customerEmail);

        if ($stmt->execute()) {
            $_SESSION['admin_success'] = "Customer details updated successfully!";
            // Update session if email is changed for the logged-in user
            if ($_SESSION['user_email'] == $customerEmail) {
                $_SESSION['user_email'] = $newEmail;
                $_SESSION['admin'] = $newAdminStatus; // Update admin status in session
            }
        } else {
            $_SESSION['admin_error'] = "Failed to update customer details.";
        }
        $stmt->close();
    } else {
        $_SESSION['admin_error'] = "All fields are required.";
    }

    header("Location: manage_customers.php");
    exit();
}
?>

<div class="container mt-4">
    <h2>Edit Customer</h2>

    <!-- Display Success/Error Messages -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <p class="text-success"><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_error'])): ?>
        <p class="text-danger"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></p>
    <?php endif; ?>

    <form action="edit_customer.php?email=<?= htmlspecialchars($customerEmail); ?>" method="post">
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" class="form-control" value="<?= htmlspecialchars($customer['firstName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="surname">Last Name:</label>
            <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($customer['surname']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" name="admin" id="admin" class="form-check-input" <?= ($customer['admin'] == 1) ? 'checked' : ''; ?>>
            <label for="admin" class="form-check-label">Grant Admin Privileges</label>
        </div>
        <button type="submit" name="update_customer" class="btn btn-primary mt-3">Update</button>
    </form>
	<a href="admin_console.php" class="btn btn-secondary mb-5">Back to Admin Console</a>
</div>

<?php include 'footer.php'; ?>
