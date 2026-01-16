<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

$db = (new Database())->getConnection();
$userId = $_SESSION['user_id'];

// Logic remains untouched
$stmt = $db->prepare("
    SELECT n.*,
    (SELECT COUNT(*) FROM notice_likes nl 
     WHERE nl.notice_id = n.id AND nl.user_id = :uid) AS liked
    FROM notices n
    ORDER BY n.created_at DESC
");
$stmt->execute(['uid' => $userId]);
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices | Smart Campus</title>
   
</head>
<body class="bg-[#f8fafc] dark:bg-gray-900">

    <div class="min-h-screen">
        <?php include "sidebar.php"; ?>
        
        <main class="p-6 md:p-10 sm:ml-64 transition-all duration-300">
            <div class="max-w-3xl mx-auto">
                
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Campus Feed</h1>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stay updated with official university announcements</p>
                    </div>
                    <div class="h-12 w-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center border border-gray-100 dark:border-gray-700 shadow-sm">
                        <i class="fa-solid fa-bell text-blue-600"></i>
                    </div>
                </div>

                <?php if (empty($notices)): ?>
                    <div class="text-center py-20">
                        <i class="fa-solid fa-layer-group text-gray-200 dark:text-gray-700 text-6xl mb-4"></i>
                        <p class="text-gray-500">No notices posted yet</p>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <?php foreach ($notices as $notice): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-6 md:p-8 shadow-sm hover:shadow-md transition-shadow">
                        
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center justify-center w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-2xl shrink-0 text-blue-600 dark:text-blue-400">
                                    <span class="text-xs font-black uppercase"><?= date('M', strtotime($notice['created_at'])) ?></span>
                                    <span class="text-lg font-bold leading-none"><?= date('d', strtotime($notice['created_at'])) ?></span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-snug">
                                        <?= htmlspecialchars($notice['title']) ?>
                                    </h2>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Official Notice</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-gray-600 dark:text-gray-300 leading-relaxed text-base mb-8">
                            <?= nl2br(htmlspecialchars($notice['description'])) ?>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Verified by Administration</span>
                            </div>

                            <form action="toggle_like.php" method="POST" class="m-0">
                                <input type="hidden" name="notice_id" value="<?= $notice['id'] ?>">
                                <button class="flex items-center gap-2.5 px-5 py-2.5 rounded-2xl transition-all font-bold text-sm
                                    <?= $notice['liked'] 
                                        ? 'bg-red-50 text-amber-600 dark:bg-red-900/20' 
                                        : 'bg-gray-50 text-emerald-600 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400' ?>">
                                    
                                    <i class="<?= $notice['liked'] ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                                    <span><?= $notice['like_count'] ?></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>
    </div>

</body>
</html>