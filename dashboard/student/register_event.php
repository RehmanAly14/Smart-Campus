<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: event_list.php");
    exit;
}

$db = (new Database())->getConnection();
$studentId = $_SESSION['user_id'];
$eventId = $_POST['event_id'];

try {
    $stmt = $db->prepare("
        INSERT INTO event_registrations (event_id, student_id)
        VALUES (:event_id, :student_id)
    ");
    $stmt->execute([
        'event_id'   => $eventId,
        'student_id'=> $studentId
    ]);
} catch (PDOException $e) {
    // Duplicate registration safely ignored
}

header("Location: event_list.php");
exit;
