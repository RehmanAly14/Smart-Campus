<?php
session_start();
require_once "../../../auth/auth_check.php";
require_once "../../../auth/role_check.php";
require_once "../../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();
$notice_id = $_GET['id'] ?? 0;

if (!$notice_id) {
    $_SESSION['error'] = "Notice ID is required";
    header("Location: notices.php");
    exit();
}

try {
    // Delete notice (cascade will delete likes too if foreign key is set)
    $stmt = $db->prepare("DELETE FROM notices WHERE id = :id");
    $stmt->execute([':id' => $notice_id]);
    
    $_SESSION['success'] = "Notice deleted successfully";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting notice: " . $e->getMessage();
}

header("Location: notice.php");
exit();
?>