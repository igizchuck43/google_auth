<?php
session_start();
require_once '../config/database.php';
require_once '../config/google-config.php';
require_once '../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $google_id = $google_account_info->id;

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE google_id = ? OR email = ?");
    $stmt->bind_param("ss", $google_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing user
        $user = $result->fetch_assoc();
        $stmt = $conn->prepare("UPDATE users SET google_id = ? WHERE id = ?");
        $stmt->bind_param("si", $google_id, $user['id']);
        $stmt->execute();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
    } else {
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, google_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $google_id);
        $stmt->execute();

        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $name;
    }

    header('Location: ../dashboard.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}
?>