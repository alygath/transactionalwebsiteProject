<?php include('header.php'); ?>
<div class="container d-flex justify-content-center align-items-center mt-4 mb-5">
    <div class="col-md-8 col-lg-6 bg-light p-4 pb-0 rounded-3">
        <!-- Title -->
        <h2 class="text-center mb-4 text-primary">Sign Up</h2>
		
        <?php 
		if (isset($_SESSION['error'])) {
    		echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
			// Unset the error message after displaying it
			unset($_SESSION['error']);
		}


		
		?>
        <form action="signup.php" method="post" id="register" onsubmit="return validateForm()">
            <div class="form-outline mb-4">
                <label class="form-label" for="forenameInput">First Name</label>
                <input type="text" name="forename" id="forenameInput" class="form-control" required />
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="surnameInput">Last Name</label>
                <input type="text" name="surname" id="surnameInput" class="form-control" required />
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="emailInput">E-mail Address</label>
                <input type="email" name="email" id="emailInput" class="form-control" required />
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="passwordInput">Password</label>
                <input type="password" name="password" id="passwordInput" class="form-control" required />
            </div>
            <div class="form-outline mb-4">
                <label class="form-label" for="passwordRepeatInput">Password Repeat</label>
                <input type="password" name="passwordRepeat" id="passwordRepeatInput" class="form-control" required />
                <small id="passwordError" class="text-danger"></small>
            </div>
            <div class="form-outline mb-4 d-flex align-items-center">
                <input type="checkbox" value="" name="terms" id="terms" class="form-check-input me-2" />
                <label class="form-label mb-0" for="terms">I have read and agreed to the <a href=".term">terms</a></label>
                <small id="termsError" class="text-danger"></small>
            </div>
            <input type="submit" class="btn btn-primary btn-block mb-0" />
        </form>
    </div>
</div>

<script>
function validateForm() {
    var password = document.getElementById("passwordInput").value;
    var confirm_password = document.getElementById("passwordRepeatInput").value;
    var terms_checked = document.getElementById("terms").checked;
    var valid = true;

    document.getElementById("passwordError").textContent = ""; // Clear previous errors
    document.getElementById("termsError").textContent = "";

    if (password != confirm_password) {
        document.getElementById("passwordError").textContent = "Passwords do not match.";
        valid = false;
    }

    if (!terms_checked) {
        document.getElementById("termsError").textContent = "You must agree to the terms and conditions.";
        valid = false;
    }

    return valid; // Only submit if valid is true
}
</script>

<?php include('footer.php'); ?>
