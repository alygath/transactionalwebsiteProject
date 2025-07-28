<?php
session_start();
include 'header.php';
?>

<div class="container mt-5">
    <h2>Payment Status</h2>
    
    <?php if (isset($_SESSION['payment_status']) && $_SESSION['payment_status'] == "success"): ?>
        <div class="alert alert-success">
            <p><?= $_SESSION['payment_message']; ?></p>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>There was an error processing your payment.</p>
        </div>
    <?php endif; ?>

    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
</div>

<?php
unset($_SESSION['payment_status']); // Clear session status
unset($_SESSION['payment_message']);
include 'footer.php';
?>
