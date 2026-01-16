<?php

require_once "../../../config/db.php";
require_once "../../../auth/auth_check.php";

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['role'] ?? 'student';

// Get filters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT n.*, u.name as author_name 
        FROM notices n 
        JOIN users u ON n.posted_by = u.id";

$params = [];

if ($filter === 'recent') {
    $sql .= " AND n.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($filter === 'popular') {
    $sql .= " ORDER BY n.like_count DESC";
}

if (!empty($search)) {
    $sql .= " AND (n.title LIKE :search OR n.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($filter !== 'popular') {
    $sql .= " ORDER BY n.created_at DESC";
}

$sql .= " LIMIT 50";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check which notices user has liked (if logged in)
    if ($user_id) {
        try {
            $like_stmt = $db->prepare("SELECT notice_id FROM notice_likes WHERE user_id = :user_id");
            $like_stmt->execute([':user_id' => $user_id]);
            $liked_notices = array_column($like_stmt->fetchAll(PDO::FETCH_ASSOC), 'notice_id');
        } catch (PDOException $e) {
            // If notice_likes table doesn't exist yet, use empty array
            $liked_notices = [];
        }
    } else {
        $liked_notices = [];
    }
    
} catch (PDOException $e) {
    $notices = [];
    $error = "Database error: " . $e->getMessage();
}

// Get stats
try {
    $stats_sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as recent,
        SUM(like_count) as total_likes
        FROM notices ";
    $stats_stmt = $db->prepare($stats_sql);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get user's total likes
    if ($user_id) {
        try {
            $user_likes_stmt = $db->prepare("SELECT COUNT(*) FROM notice_likes WHERE user_id = :user_id");
            $user_likes_stmt->execute([':user_id' => $user_id]);
            $stats['my_likes'] = $user_likes_stmt->fetchColumn();
        } catch (PDOException $e) {
            $stats['my_likes'] = 0;
        }
    } else {
        $stats['my_likes'] = 0;
    }
    
} catch (PDOException $e) {
    $stats = ['total' => 0, 'recent' => 0, 'total_likes' => 0, 'my_likes' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices | SmartCampus</title>
   
</head>
<body class="bg-gray-50">

<?php include "../../../includes/dashboard_sidebar.php"; ?>

<div class=" sm:ml-64 transition-all duration-300">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Campus Notices</h1>
                <p class="text-gray-600 text-sm mt-1">Stay updated with important announcements</p>
            </div>
            <?php if($user_role === 'admin'): ?>
            <a href="add_notice.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-0 text-sm lg:text-lg lg:gap-2 transition duration-200">
                <i class="fas fa-plus"></i>
                Create Notice
            </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="p-8">
        <!-- Display Errors -->
        <?php if(isset($error)): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-red-700"><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats & Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Notices</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">This Week</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['recent'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="fas fa-calendar-week text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">My Likes</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['my_likes'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fas fa-heart text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Likes</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['total_likes'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fas fa-thumbs-up text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <form method="GET" class="w-full">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search notices..." 
                                   value="<?= htmlspecialchars($search) ?>"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </form>
                    </div>
                </div>

                <!-- Filter -->
                <div>
                    <select name="filter" 
                            onchange="window.location.href = updateUrlParam('filter', this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Notices</option>
                        <option value="recent" <?= $filter === 'recent' ? 'selected' : '' ?>>Recent (7 days)</option>
                        <option value="popular" <?= $filter === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div>
                    <button onclick="clearFilters()" class="w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-filter-circle-xmark"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Notices List -->
        <div class="space-y-6">
            <?php if (empty($notices)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-bullhorn text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-700 mb-2">No notices found</h4>
                <p class="text-gray-500 mb-6">
                    <?php if (!empty($search)): ?>
                        No notices match your search criteria
                    <?php else: ?>
                        No notices have been published yet
                    <?php endif; ?>
                </p>
                <?php if (!empty($search)): ?>
                <button onclick="window.location.href = window.location.pathname" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                    Clear Search
                </button>
                <?php elseif($user_role === 'admin'): ?>
                <a href="add_notice.php" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Create First Notice
                </a>
                <?php endif; ?>
            </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): 
                    $is_liked = in_array($notice['id'], $liked_notices);
                    $time_ago = time_ago($notice['created_at']);
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200" id="notice-<?= $notice['id'] ?>">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($notice['title']) ?></h3>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars($notice['author_name']) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        <?= $time_ago ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-heart text-red-500"></i>
                                        <span id="like-count-<?= $notice['id'] ?>"><?= $notice['like_count'] ?></span> likes
                                    </span>
                                </div>
                            </div>
                            <?php if($user_role === 'admin'): ?>
                            <div class="flex items-center gap-2">
                                <button onclick="deleteNotice(<?= $notice['id'] ?>)" 
                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition duration-150"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="prose max-w-none mb-6 text-gray-700">
                            <?= nl2br(htmlspecialchars($notice['description'])) ?>
                        </div>
                        
                        <!-- Like & Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-4">
                                <!-- Like Button -->
                                <?php if($user_id): ?>
                                <button onclick="toggleLike(<?= $notice['id'] ?>)" 
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition duration-150 <?= $is_liked ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' ?>"
                                        id="like-btn-<?= $notice['id'] ?>">
                                    <i class="<?= $is_liked ? 'fas fa-heart' : 'far fa-heart' ?>" id="like-icon-<?= $notice['id'] ?>"></i>
                                    <span class="font-medium"><?= $notice['like_count'] ?></span>
                                    <span>Like<?= $notice['like_count'] != 1 ? 's' : '' ?></span>
                                </button>
                                <?php else: ?>
                                <button onclick="alert('Please login to like notices')" 
                                        class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition duration-150">
                                    <i class="far fa-heart"></i>
                                    <span class="font-medium"><?= $notice['like_count'] ?></span>
                                    <span>Like<?= $notice['like_count'] != 1 ? 's' : '' ?></span>
                                </button>
                                <?php endif; ?>
                                
                                <!-- Comment Button -->
                                <button onclick="showComments(<?= $notice['id'] ?>)" 
                                        class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition duration-150">
                                    <i class="far fa-comment"></i>
                                    <span>Comment</span>
                                </button>
                                
                                <!-- Share Button -->
                                <button onclick="shareNotice(<?= $notice['id'] ?>)" 
                                        class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition duration-150">
                                    <i class="fas fa-share"></i>
                                    <span>Share</span>
                                </button>
                            </div>
                            
                            <div class="text-sm text-gray-500">
                                Notice #<?= $notice['id'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Load More -->
        <?php if(count($notices) >= 50): ?>
        <div class="mt-8 text-center">
            <button onclick="loadMoreNotices()" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200">
                Load More Notices
            </button>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- JavaScript for Like System -->
<script>
// URL helper functions
function updateUrlParam(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    return url.toString();
}

function clearFilters() {
    window.location.href = window.location.pathname;
}

// Like/Unlike function
async function toggleLike(noticeId) {
    try {
        const response = await fetch('notice_like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notice_id=${noticeId}&action=toggle_like`
        });
        
        const data = await response.json();
        
        if (data.success) {
            const likeBtn = document.getElementById(`like-btn-${noticeId}`);
            const likeIcon = document.getElementById(`like-icon-${noticeId}`);
            const likeCount = document.getElementById(`like-count-${noticeId}`);
            const likeText = likeBtn.querySelector('span:nth-child(3)');
            
            // Update like count
            likeCount.textContent = data.like_count;
            
            // Update button appearance
            if (data.liked) {
                likeBtn.classList.remove('bg-gray-50', 'text-gray-600');
                likeBtn.classList.add('bg-red-50', 'text-red-600');
                likeIcon.classList.remove('far', 'fa-heart');
                likeIcon.classList.add('fas', 'fa-heart');
            } else {
                likeBtn.classList.remove('bg-red-50', 'text-red-600');
                likeBtn.classList.add('bg-gray-50', 'text-gray-600');
                likeIcon.classList.remove('fas', 'fa-heart');
                likeIcon.classList.add('far', 'fa-heart');
            }
            
            // Update "Likes" text
            likeText.textContent = data.like_count == 1 ? 'Like' : 'Likes';
            
            // Update stats if on page
            updateStats();
            
        } else {
            alert(data.message || 'Error processing like');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    }
}

function updateStats() {
    // Simple page reload to update stats
    // You could make this AJAX later
    // location.reload();
}

// Other functions
function editNotice(id) {
    window.location.href = `edit_notice.php?id=${id}`;
}

function deleteNotice(id) {
    if (confirm('Are you sure you want to delete this notice?')) {
        window.location.href = `delete_notice.php?id=${id}`;
    }
}

function showComments(id) {
    alert('Comment system coming soon!');
}

function shareNotice(id) {
    const noticeTitle = document.querySelector(`#notice-${id} h3`).textContent;
    const shareUrl = `${window.location.origin}${window.location.pathname}?notice=${id}`;
    
    if (navigator.share) {
        navigator.share({
            title: noticeTitle,
            text: 'Check out this notice from SmartCampus',
            url: shareUrl
        });
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(shareUrl).then(() => {
            alert('Notice URL copied to clipboard!');
        });
    }
}

function loadMoreNotices() {
    alert('Load more functionality coming soon!');
}

// Auto submit search on enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
</script>

</body>
</html>

<?php
// Helper function to display time ago
function time_ago($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins != 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $time);
    }
}
?>