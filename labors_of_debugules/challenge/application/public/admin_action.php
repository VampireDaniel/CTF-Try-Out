<?php
require_once('../includes/db.php');
require_once('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'edit_user') {
        $user_id = $_POST['user_id'] ?? '';
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'user';

        // Validate user_id
        if (!ctype_digit($user_id)) {
            $_SESSION['error'] = "Invalid user ID.";
            header('Location: admin.php');
            exit();
        }

        // Additional validation can be added here (e.g., email format, username uniqueness)

        $success = updateUser($user_id, $name, $username, $email, $role);

        if ($success) {
            $_SESSION['success'] = "User updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update user.";
        }

        header('Location: admin.php');
        exit();

    } elseif ($action === 'reset_password') {
        $user_id = $_POST['user_id'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        // Validate user_id
        if (!ctype_digit($user_id)) {
            $_SESSION['error'] = "Invalid user ID.";
            header('Location: admin.php');
            exit();
        }

        // Additional validation for password strength can be added here

        $success = resetUserPassword($user_id, $new_password);

        if ($success) {
            $_SESSION['success'] = "Password reset successfully.";
        } else {
            $_SESSION['error'] = "Failed to reset password.";
        }

        header('Location: admin.php');
        exit();
    }
}

// If action is not recognized
$_SESSION['error'] = "Invalid action.";
header('Location: admin.php');
exit();
?>
