<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: complaints.php");
    exit;
}

$category = trim($_POST['category']);
$description = trim($_POST['description']);
$userId = $_SESSION['user_id'];

if (empty($category) || empty($description)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: complaints.php");
    exit;
}

$db = (new Database())->getConnection();

$stmt = $db->prepare("
    INSERT INTO complaints (user_id, category, description, status)
    VALUES (:uid, :category, :description, 'pending')
");

$stmt->execute([
    'uid' => $userId,
    'category' => $category,
    'description' => $description
]);

$_SESSION['success'] = "Complaint submitted successfully.";
header("Location: complaints.php");
exit;
