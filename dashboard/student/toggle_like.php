<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: notices.php");
    exit;
}

$noticeId = (int)$_POST['notice_id'];
$userId = $_SESSION['user_id'];

$db = (new Database())->getConnection();

// Check if already liked
$check = $db->prepare("
    SELECT id FROM notice_likes
    WHERE notice_id = :nid AND user_id = :uid
");
$check->execute([
    'nid' => $noticeId,
    'uid' => $userId
]);

if ($check->rowCount() > 0) {
    // Unlike
    $db->prepare("
        DELETE FROM notice_likes
        WHERE notice_id = :nid AND user_id = :uid
    ")->execute([
        'nid' => $noticeId,
        'uid' => $userId
    ]);

    $db->prepare("
        UPDATE notices SET like_count = like_count - 1
        WHERE id = :nid
    ")->execute(['nid' => $noticeId]);

} else {
    // Like
    $db->prepare("
        INSERT INTO notice_likes (notice_id, user_id)
        VALUES (:nid, :uid)
    ")->execute([
        'nid' => $noticeId,
        'uid' => $userId
    ]);

    $db->prepare("
        UPDATE notices SET like_count = like_count + 1
        WHERE id = :nid
    ")->execute(['nid' => $noticeId]);
}

header("Location: notices.php");
exit;
