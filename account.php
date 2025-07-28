<?php 
session_start();
include('header.php'); 

if (isset($_SESSION['user_email'])): ?>
    <div class="container mt-4 mb-5">
        <h2 class="mb-3">Account Details</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['update_success'])): ?>
            <p class="text-success"><?= $_SESSION['update_success']; unset($_SESSION['update_success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['update_error'])): ?>
            <p class="text-danger"><?= $_SESSION['update_error']; unset($_SESSION['update_error']); ?></p>
        <?php endif; ?>

        <!-- Update Profile Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Update Profile</h4>
                <form action="update_profile.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <?php
                        // Generate profile picture path based on email
                        $emailFilename = preg_replace("/[^a-zA-Z0-9]/", "_", $_SESSION['user_email']) . ".png";
                        $profilePicPath = "images/profilePics/" . $emailFilename;

                        // Check if the file exists, otherwise use default image
                        $profilePic = file_exists($profilePicPath) ? $profilePicPath : "images/base.jpg";
                        ?>

                        <!-- Profile Picture -->
                        <img src="<?= htmlspecialchars($profilePic) ?>" 
                             alt="Profile Picture" 
                             class="img-thumbnail img-fluid"
                             style="max-width: 200px; max-height: 200px;">

                        <!-- Profile Picture Upload -->
                        <div class="form-group mt-3">
                            <label for="profilePic">Upload New Profile Picture:</label>
                            <input type="file" name="profilePic" id="profilePic" class="form-control-file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="forename">First Name:</label>
                        <input type="text" name="forename" id="forename" class="form-control" value="<?= htmlspecialchars($_SESSION['user_forename']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="surname">Last Name:</label>
                        <input type="text" name="surname" id="surname" class="form-control" value="<?= htmlspecialchars($_SESSION['user_surname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-3">Change Password</h4>
                <form action="update_profile.php" method="post">
                    <input type="hidden" name="update_type" value="change_password"> <!-- Identify form submission -->

                    <div class="form-group">
                        <label for="currentPassword">Current Password:</label>
                        <input type="password" name="currentPassword" id="currentPassword" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="newPassword">New Password:</label>
                        <input type="password" name="newPassword" id="newPassword" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password:</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Change Password</button>
                </form>
            </div>
        </div>

        <!-- User Actions Section -->
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Your Actions</h4>
                <div class="d-flex flex-wrap gap-3">
                    <!-- View Orders for Customers -->
                    <a href="customerOrders.php" class="btn btn-primary">View Your Orders</a>

                    <!-- Admin Panel Access -->
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                        <a href="admin_console.php" class="btn btn-warning">Admin Console</a>
                    <?php endif; ?>

                    <!-- Logout Button (Properly Aligned) -->
                    <a href="logout.php" class="btn btn-danger">Log Out</a>
                </div>
            </div>
        </div>

    </div>
<?php else: ?>
    <p class="text-center mt-4">You are not logged in. Please <a href='login.php'>log in</a>.</p>
<?php endif; ?>

<?php include('footer.php'); ?>
