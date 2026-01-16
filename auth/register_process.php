<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role'] ?? '');

$errors = [];

if ($name === '') $errors[] = "Name is required";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required";
if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
if (!in_array($role, ['student','faculty','admin'])) $errors[] = "Invalid role";

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: register.php");
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check email
    $check = $db->prepare("SELECT id FROM users WHERE email = :email");
    $check->bindParam(":email", $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        $_SESSION['errors'] = ["Email already exists"];
        header("Location: register.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insert = $db->prepare("
        INSERT INTO users (name, email, password, role, is_active)
        VALUES (:name, :email, :password, :role, 1)
    ");

    $insert->execute([
        ':name'     => $name,
        ':email'    => $email,
        ':password' => $hashedPassword,
        ':role'     => $role
    ]);

    // Auto login
    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['name'] = $name;
    $_SESSION['role'] = $role;
    $_SESSION['logged_in'] = true;

    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['errors'] = ["Registration failed"];
    header("Location: register.php");
    exit;
}
