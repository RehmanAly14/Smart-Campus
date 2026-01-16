<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$errors = [];

if ($email === '') $errors[] = "Email is required";
if ($password === '') $errors[] = "Password is required";

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: login.php");
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $user['password'])) {
            $_SESSION['errors'] = ["Invalid email or password"];
            header("Location: login.php");
            exit;
        }

        if ((int)$user['is_active'] === 0) {
            $_SESSION['errors'] = ["Your account is deactivated"];
            header("Location: login.php");
            exit;
        }

        // ✅ LOGIN SUCCESS
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['logged_in'] = true;

        header("Location: ../index.php");
        exit;
    }

    $_SESSION['errors'] = ["Invalid email or password"];
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['errors'] = ["Something went wrong. Please try again."];
    header("Location: login.php");
    exit;
}
