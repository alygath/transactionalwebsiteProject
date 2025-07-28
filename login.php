<?php include ('header.php'); ?>
<div class="container">
    <div class="mb-4 mt-4" style="background-color:#efefef ">
        <div class="p-4">
            <?php 
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            ?>
            <form action="signin.php" method="post" id="signin">
                <div class="form-outline mb-4">
                    <input type="email" name="email" id="emailInput" class="form-control" required />
                    <label class="form-label" for="emailInput">E-mail Address</label>
                </div>
                
                <div class="form-outline mb-4">
                    <input type="password" name="password" id="passwordInput" class="form-control" required />
                    <label class="form-label" for="passwordInput">Password</label>
                </div>
                <input type="submit" class="btn btn-primary btn-block mb-4" value="Log In" />
            </form>
            <div class="row">
                <div class="text-center">
                    <a href="forgotPassword.php">Forgot Password</a>
                    <p>Not a member? <a href="./register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('footer.php'); ?>
