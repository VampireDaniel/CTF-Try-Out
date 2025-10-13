<?php
require_once('../includes/db.php');

/**
 * Fetch all users from the database.
 *
 * @return array An array of users.
 */
function getAllUsers() {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $query = "SELECT user_id, name, username, email, role FROM Users";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update a user's information.
 *
 * @param int $user_id The ID of the user to update.
 * @param string $name The new name of the user.
 * @param string $username The new username.
 * @param string $email The new email address.
 * @param string $role The new role ('user' or 'administrator').
 * @return bool True on success, False on failure.
 */
function updateUser($user_id, $name, $username, $email, $role) {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $query = "UPDATE Users SET name = :name, username = :username, email = :email, role = :role WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Reset a user's password.
 *
 * @param int $user_id The ID of the user.
 * @param string $new_password The new password in plain text.
 * @return bool True on success, False on failure.
 */
function resetUserPassword($user_id, $new_password) {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $hashed_password = hash('sha256', $new_password);

    $query = "UPDATE Users SET password = :password WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}
?>
