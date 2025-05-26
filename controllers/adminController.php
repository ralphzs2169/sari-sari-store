<?php
session_start();

require_once "../config/database.php";
require_once "../models/adminModel.php";

$db = new Database();
$conn = $db->getConnection();
$user = new Admin($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Store entered values for form persistence
        $_SESSION['old_reg_username'] = $username;
        $_SESSION['old_reg_password'] = $password;
        $_SESSION['old_reg_confirm'] = $confirm_password;

        // Custom validation with specific error messages
        if (empty($username)) {
            $_SESSION['register_error'] = "Please enter a username.";
        } elseif (empty($password)) {
            $_SESSION['register_error'] = "Please enter a password.";
        } elseif (empty($confirm_password)) {
            $_SESSION['register_error'] = "Please confirm your password.";
        } elseif ($password !== $confirm_password) {
            $_SESSION['register_error'] = "Passwords do not match. Please try again.";
        } elseif (strlen($password) < 6) {
            $_SESSION['register_error'] = "Password must be at least 6 characters long.";
        } else {
            // Check if username already exists
            $existingUser = $user->login($username);
            if ($existingUser) {
                $_SESSION['register_error'] = "Username already exists. Please choose a different username.";
            } else {
                // Success - create user and clear session data
                $user->create($username, $password);
                unset($_SESSION['register_error'], $_SESSION['old_reg_username'], $_SESSION['old_reg_password'], $_SESSION['old_reg_confirm']);
                $_SESSION['success'] = "Account created successfully! Please login.";
                header("Location: ../views/login.php");
                exit;
            }
        }

        header("Location: ../views/login.php?show=register");
        exit;
        break;

    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Store entered values for form persistence
        $_SESSION['old_username'] = $username;
        $_SESSION['old_password'] = $password;

        // Custom validation with specific error messages
        if (empty($username)) {
            $_SESSION['error'] = "Please enter your username.";
        } elseif (empty($password)) {
            $_SESSION['error'] = "Please enter your password.";
        } else {
            $admin = $user->login($username);

            if (!$admin) {
                $_SESSION['error'] = "Username not found. Please check your username.";
            } elseif (!password_verify($password, $admin['password'])) {
                $_SESSION['error'] = "Incorrect password. Please try again.";
            } else {
                // Success - clear ALL session data including old inputs
                unset($_SESSION['error'], $_SESSION['old_username'], $_SESSION['old_password']);
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: ../views/dashboard.php");
                exit;
            }
        }

        header("Location: ../views/login.php");
        exit;
        break;


    case 'logout':
        session_destroy();
        header("Location: ../views/login.php");
        break;
}
