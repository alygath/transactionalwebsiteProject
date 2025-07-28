<?php
session_start();
include 'connection.php';
include 'header.php';

// Ensure user is an admin
if (!isset($_SESSION['user_email']) || $_SESSION['admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Fetch search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query dynamically
$sql = "SELECT email, firstName, surname FROM users WHERE 1=1";
$params = [];
$types = "";

if (!empty($searchQuery)) {
    $sql .= " AND (email LIKE ? OR firstName LIKE ? OR surname LIKE ?)";
    $likeQuery = "%{$searchQuery}%";
    $params = [$likeQuery, $likeQuery, $likeQuery];
    $types = "sss";
}

// Prepare and execute query
$stmt = $connection->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$customers = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Manage Customers</h2>

    <!-- 🔍 Search & Filter Form -->
    <form action="manage_customers.php" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Email, First or Last Name" value="<?= htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($customer = $customers->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($customer['email']); ?></td>
                    <td><?= htmlspecialchars($customer['firstName']); ?></td>
                    <td><?= htmlspecialchars($customer['surname']); ?></td>
                    <td>
                        <a href="edit_customer.php?email=<?= urlencode($customer['email']); ?>" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
	<a href="admin_console.php" class="btn btn-secondary">Back to Admin Console</a>
</div>

<?php include 'footer.php'; ?>
