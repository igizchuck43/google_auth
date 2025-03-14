<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit();
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User added successfully";
    } else {
        $_SESSION['error'] = "Error adding user";
    }
    header('Location: users.php');
    exit();
}

// Handle Delete User
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']); // Prevent self-deletion

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success'] = "User deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting user";
    }
    header('Location: users.php');
    exit();
}

// Handle Update User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "UPDATE users SET name = ?, email = ?";
    $params = ["ss", $name, $email];

    // Update password only if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[0] .= "s";
        $params[] = $password;
    }

    $sql .= " WHERE id = ?";
    $params[0] .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    call_user_func_array([$stmt, 'bind_param'], $params);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully";
    } else {
        $_SESSION['error'] = "Error updating user";
    }
    header('Location: users.php');
    exit();
}

header('Location: users.php');
?>