<?php
session_start();
require_once "../../../config/db.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please login to like notices'
    ]);
    exit();
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Get input
$notice_id = (int)($_POST['notice_id'] ?? 0);

if (!$notice_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Notice ID is required'
    ]);
    exit();
}

try {
    // Check if user already liked this notice
    $check_stmt = $db->prepare("SELECT id FROM notice_likes WHERE notice_id = :notice_id AND user_id = :user_id");
    $check_stmt->execute([':notice_id' => $notice_id, ':user_id' => $user_id]);
    $already_liked = $check_stmt->fetch();
    
    if ($already_liked) {
        // Unlike
        $delete_stmt = $db->prepare("DELETE FROM notice_likes WHERE notice_id = :notice_id AND user_id = :user_id");
        $delete_stmt->execute([':notice_id' => $notice_id, ':user_id' => $user_id]);
        
        $update_stmt = $db->prepare("UPDATE notices SET like_count = like_count - 1 WHERE id = :notice_id");
        $update_stmt->execute([':notice_id' => $notice_id]);
        
        $liked = false;
    } else {
        // Like
        $insert_stmt = $db->prepare("INSERT INTO notice_likes (notice_id, user_id, created_at) VALUES (:notice_id, :user_id, NOW())");
        $insert_stmt->execute([':notice_id' => $notice_id, ':user_id' => $user_id]);
        
        $update_stmt = $db->prepare("UPDATE notices SET like_count = like_count + 1 WHERE id = :notice_id");
        $update_stmt->execute([':notice_id' => $notice_id]);
        
        $liked = true;
    }
    
    // Get updated like count
    $count_stmt = $db->prepare("SELECT like_count FROM notices WHERE id = :notice_id");
    $count_stmt->execute([':notice_id' => $notice_id]);
    $like_count = (int)$count_stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'like_count' => $like_count
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>