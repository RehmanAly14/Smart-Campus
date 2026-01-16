<?php
session_start();
require_once "../../../auth/auth_check.php";
require_once "../../../auth/role_check.php";
require_once "../../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();

// Get action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$user_id = $_POST['user_id'] ?? $_GET['id'] ?? 0;

if (!$user_id) {
    $_SESSION['error'] = "User ID is required";
    header("Location: users.php");
    exit();
}

// Check if user is trying to modify themselves
if ($user_id == $_SESSION['user_id'] && in_array($action, ['toggle_status', 'delete'])) {
    $_SESSION['error'] = "You cannot modify your own account status or delete yourself";
    header("Location: users.php");
    exit();
}

try {
    switch ($action) {
        case 'update_role':
            $new_role = $_POST['role'] ?? '';
            if (!in_array($new_role, ['admin', 'faculty', 'student'])) {
                $_SESSION['error'] = "Invalid role specified";
                break;
            }
            
            $stmt = $db->prepare("UPDATE users SET role = :role WHERE id = :id");
            $stmt->execute([':role' => $new_role, ':id' => $user_id]);
            $_SESSION['success'] = "User role updated successfully";
            break;
            
        case 'toggle_status':
            // Get current status
            $stmt = $db->prepare("SELECT is_active FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $new_status = $user['is_active'] ? 0 : 1;
                $stmt = $db->prepare("UPDATE users SET is_active = :status WHERE id = :id");
                $stmt->execute([':status' => $new_status, ':id' => $user_id]);
                $_SESSION['success'] = $new_status ? "User activated successfully" : "User deactivated successfully";
            }
            break;
            
        case 'delete':
            // Hard delete (no soft delete in your schema)
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "User deleted successfully";
            } else {
                $_SESSION['error'] = "User not found or already deleted";
            }
            break;
            
        case 'reset_password':
            // Generate random password (8 characters)
            $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute([':password' => $hashed_password, ':id' => $user_id]);
            
            // Get user info for display
            $stmt = $db->prepare("SELECT email, name FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Store in session for display (for demo purposes)
                $_SESSION['reset_info'] = [
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'password' => $new_password
                ];
            }
            
            $_SESSION['success'] = "Password reset successfully. New password: " . $new_password;
            break;
            
        default:
            $_SESSION['error'] = "Invalid action specified";
            break;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

// Redirect back to users page
header("Location: users_list.php");
exit();