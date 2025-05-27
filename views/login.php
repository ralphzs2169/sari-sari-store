<?php
session_start();

// Capture register error state before any unset operations
$hasRegisterError = isset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>JRJ Sari-Sari Store - Login</title>
    <link rel="stylesheet" href="../assets/css/login.css" />
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>

<body data-register-error="<?= $hasRegisterError ? 'true' : 'false' ?>">
    <div class="container">
        <div class="auth-container">
            <!-- Login Form -->
            <div id="loginForm">
                <div class="auth-card">
                    <div class="store-header">
                        <div class="store-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h2 class="store-title">Tindahan ni Aling Rosa</h2>
                        <p class="store-subtitle">Sari-Sari Store Management System</p>
                    </div>

                    <form id="loginFormElement" action="../controllers/adminController.php?action=login" method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="loginUsername" name="username" placeholder="Username" value="<?php echo isset($_SESSION['old_username']) ? htmlspecialchars($_SESSION['old_username']) : ''; ?>" />
                            <label for="loginUsername"><i class="fas fa-user me-2"></i>Username</label>
                        </div>

                        <div class="form-floating password-field">
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Password" value="<?php echo isset($_SESSION['old_password']) ? htmlspecialchars($_SESSION['old_password']) : ''; ?>" />
                            <label for="loginPassword"><i class="fas fa-lock me-2"></i>Password</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')">
                                <i class="fas fa-eye" id="loginPasswordToggle"></i>
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>

                    <?php if (isset($_SESSION['error'])) : ?>
                        <div id="loginError" class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="loginErrorText"><?php echo $_SESSION['error']; ?></span>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])) : ?>
                        <div id="loginSuccess" class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <span><?php echo $_SESSION['success']; ?></span>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <div class="divider mt-4">
                        <span>Don't have an account?</span>
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-2" onclick="showRegisterForm()">
                        <i class="fas fa-user-plus me-2"></i>Create New Account
                    </button>
                </div>
            </div>

            <!-- Register Form -->
            <div id="registerForm" class="hidden">
                <div class="auth-card">
                    <div class="store-header">
                        <div class="store-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2 class="store-title">Create Account</h2>
                        <p class="store-subtitle">Join Tindahan ni Aling Rosa</p>
                    </div>

                    <form id="registerFormElement" action="../controllers/adminController.php?action=create" method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="registerUsername" name="username" placeholder="Username" value="<?php echo isset($_SESSION['old_reg_username']) ? htmlspecialchars($_SESSION['old_reg_username']) : ''; ?>" />
                            <label for="registerUsername"><i class="fas fa-user me-2"></i>Username</label>
                        </div>

                        <div class="form-floating password-field">
                            <input type="password" class="form-control" id="registerPassword" name="password" placeholder="Password" value="<?php echo isset($_SESSION['old_reg_password']) ? htmlspecialchars($_SESSION['old_reg_password']) : ''; ?>" />
                            <label for="registerPassword"><i class="fas fa-lock me-2"></i>Password</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('registerPassword')">
                                <i class="fas fa-eye" id="registerPasswordToggle"></i>
                            </button>
                        </div>

                        <div class="form-floating password-field">
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm Password" value="<?php echo isset($_SESSION['old_reg_confirm']) ? htmlspecialchars($_SESSION['old_reg_confirm']) : ''; ?>" />
                            <label for="confirmPassword"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye" id="confirmPasswordToggle"></i>
                            </button>
                        </div>

                        <?php if ($hasRegisterError) : ?>
                            <div id="passwordAlert" class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span id="passwordAlertText"><?php echo $_SESSION['register_error']; ?></span>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>

                    <div class="divider mt-4">
                        <span>Already have an account?</span>
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-2" onclick="showLoginForm()">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In Instead
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/jquery/js/jquery-3.7.1.js"></script>
    <script src="../assets/jquery/js/jquery-ui.min.js"></script>
    <script src="../assets/js/login.js"></script>

    <?php
    // Clear session variables AFTER everything is rendered and JavaScript has executed
    if ($hasRegisterError) {
        unset($_SESSION['register_error']);
    }
    // Clear other session variables
    unset($_SESSION['old_reg_username']);
    unset($_SESSION['old_reg_password']);
    unset($_SESSION['old_reg_confirm']);
    unset($_SESSION['old_username']);
    unset($_SESSION['old_password']);
    ?>
</body>

</html>