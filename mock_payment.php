<?php
session_start();
include 'header.php';
?>

<div class="container mt-5">
    <h2>Mock Payment</h2>
    <p class="text-muted">Enter your card details to complete the payment (this is a simulation).</p>

    <div class="card p-4">
        <form action="mock_process_payment.php" method="POST">
            <div class="form-group">
                <label for="card_number">Card Number:</label>
                <input type="text" name="card_number" id="card_number" class="form-control" placeholder="1234 5678 9101 1121" required>
            </div>

            <div class="form-group">
                <label for="card_holder">Cardholder Name:</label>
                <input type="text" name="card_holder" id="card_holder" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="col-md-6">
                    <label for="expiry_date">Expiry Date:</label>
                    <input type="text" name="expiry_date" id="expiry_date" class="form-control" placeholder="MM/YY" required>
                </div>
                <div class="col-md-6">
                    <label for="cvv">CVV:</label>
                    <input type="text" name="cvv" id="cvv" class="form-control" placeholder="123" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-3 btn-block">Simulate Payment</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
