<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

$db = (new Database())->getConnection();
$userId = $_SESSION['user_id'];

// Logic remains untouched
$stmt = $db->prepare("
    SELECT * FROM complaints
    WHERE user_id = :uid
    ORDER BY created_at DESC
");
$stmt->execute(['uid' => $userId]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints | Smart Campus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] dark:bg-gray-900 text-slate-900 dark:text-slate-200">

<div class=" min-h-screen">
    <?php include "sidebar.php"; ?>

    <main class=" p-6 md:p-10 sm:ml-64 transition-all duration-300">
        
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">Support Desk</h1>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Submit tickets and track resolution progress</p>
            </div>
            <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center border border-gray-100 dark:border-gray-700 shadow-sm">
                <i class="fa-solid fa-headset text-blue-600"></i>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-100 text-emerald-800 p-4 rounded-2xl">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-sm font-bold"><?= htmlspecialchars($success) ?></span>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-100 text-red-800 p-4 rounded-2xl">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span class="text-sm font-bold"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-1">
                <form action="add_complaint.php" method="POST"
                      class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm sticky top-10">
                    
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-blue-600"></i>
                        New Ticket
                    </h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Category</label>
                            <select name="category" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-medium text-sm">
                                <option value="">Select Department</option>
                                <option value="IT">Hostel</option>
                                <option value="Hostel">Classroom</option>
                                <option value="Academics">Lab</option>
                                <option value="Accounts">Internet</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Issue Description</label>
                            <textarea name="description" rows="5" required
                                      placeholder="Provide details about the issue..."
                                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-medium text-sm"></textarea>
                        </div>

                        <button class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold  rounded-xl shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                            Submit 
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 mt-4 space-y-6">
                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-400 mb-2">Ticket History</h2>
                
                <?php if (empty($complaints)): ?>
                    <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-inbox text-5xl text-gray-100 dark:text-gray-700 mb-4"></i>
                        <p class="text-gray-500 font-medium">No complaints found.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($complaints as $c): ?>
                <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900/40 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-folder-open text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($c['category']) ?></h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Reference #TIC-<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></p>
                            </div>
                        </div>

                        <span class="text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full
                            <?= $c['status'] === 'pending' ? 'bg-orange-50 text-orange-600  border-orange-100' :
                               ($c['status'] === 'in_progress' ? 'bg-blue-50 text-blue-600  border-blue-100' :
                                'bg-emerald-50 text-emerald-600 border border-emerald-100') ?>">
                            <i class="fa-solid fa-circle text-[6px] mr-1.5 align-middle"></i>
                            <?= ucfirst(str_replace('_',' ', $c['status'])) ?>
                        </span>
                    </div>

                    <div class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6 text-sm">
                        <?= nl2br(htmlspecialchars($c['description'])) ?>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-50 dark:border-gray-700/50">
                        <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                            <i class="fa-regular fa-calendar"></i>
                            <?= date('M d, Y', strtotime($c['created_at'])) ?>
                            <span class="mx-1 opacity-30">•</span>
                            <i class="fa-regular fa-clock"></i>
                            <?= date('h:i A', strtotime($c['created_at'])) ?>
                        </div>
                        <button class="text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline">View Details</button>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>
</div>

</body>
</html>