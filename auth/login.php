<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: ../index.php");
        exit();
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];

            if ($user['is_admin']) {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        }
    }

    $_SESSION['error'] = "Invalid email or password";
    header("Location: ../index.php");
    exit();
}
?>