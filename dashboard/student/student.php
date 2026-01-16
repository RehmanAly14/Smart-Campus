<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

$db = (new Database())->getConnection();
$userId = $_SESSION['user_id'];

// Stats logic remains exactly as you had it
$totalEvents = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$myEvents = $db->query("SELECT COUNT(*) FROM event_registrations WHERE id = $userId")->fetchColumn();
$myComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE user_id = $userId")->fetchColumn();
$notices = $db->query("SELECT COUNT(*) FROM notices")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Smart Campus</title>
   
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .card-flat {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-flat:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        .dark .card-flat:hover {
            background-color: #1e293b;
        }
        .action-card {
            transition: transform 0.2s ease;
        }
        .action-card:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body class="bg-[#fcfcfd] dark:bg-[#0f172a] text-slate-900 dark:text-slate-200">

<div class=" min-h-screen">

    <?php include "sidebar.php"; ?>

    <main class=" p-8 sm:ml-64 transition-all duration-300">

        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Welcome back, <span class="text-blue-600 dark:text-blue-400"><?= htmlspecialchars($_SESSION['name']) ?></span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Here is what's happening on campus today.
                </p>
            </div>
            <div class="hidden md:block">
                <span class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                    Student Portal
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            
            <div class="card-flat  bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="fa-solid fa-calendar-days text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Total</p>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Events</p>
                    </div>
                </div>
                <h2 class="text-3xl font-bold"><?= $totalEvents ?></h2>
            </div>

            <div class="card-flat bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Joined</p>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">My Events</p>
                    </div>
                </div>
                <h2 class="text-3xl font-bold"><?= $myEvents ?></h2>
            </div>

            <div class="card-flat bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <i class="fa-solid fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Active</p>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Complaints</p>
                    </div>
                </div>
                <h2 class="text-3xl font-bold"><?= $myComplaints ?></h2>
            </div>

            <div class="card-flat bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <i class="fa-solid fa-bullhorn text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Board</p>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Notices</p>
                    </div>
                </div>
                <h2 class="text-3xl font-bold"><?= $notices ?></h2>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-6">Service Shortcuts</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <a href="event_list.php" class="action-card bg-slate-900 dark:bg-slate-800 p-6 rounded-2xl border border-slate-800 dark:border-slate-700 shadow-lg text-white">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-calendar-days text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold">Discover Events</h3>
                    <p class="text-slate-400 text-sm mt-1">Join workshops and seminars</p>
                    <div class="mt-4 flex items-center text-blue-400 text-xs font-bold uppercase tracking-wider">
                        Explore <i class="fa-solid fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <a href="complaints.php" class="action-card bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center mb-4 text-white">
                        <i class="fa-solid fa-circle-question text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold">Help Desk</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Submit a formal complaint</p>
                    <div class="mt-4 flex items-center text-orange-500 text-xs font-bold uppercase tracking-wider">
                        Request Support <i class="fa-solid fa-arrow-right ml-2"></i>
                    </div>
                </a>

                <a href="notices.php" class="action-card bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center mb-4 text-white">
                        <i class="fa-solid fa-newspaper text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold">Latest Notices</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Official board announcements</p>
                    <div class="mt-4 flex items-center text-emerald-500 text-xs font-bold uppercase tracking-wider">
                        View Feed <i class="fa-solid fa-arrow-right ml-2"></i>
                    </div>
                </a>

            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <h2 class="text-lg font-bold">Recent Updates</h2>
                <i class="fa-solid fa-clock-rotate-left text-slate-300"></i>
            </div>
            <div class="p-6">
                <div class="flex items-center p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mr-4 text-blue-600">
                        <i class="fa-solid fa-user-check text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold">Security Update</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Your student dashboard has been upgraded.</p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 bg-white dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">JUST NOW</span>
                </div>
                
                <div class="py-12 text-center">
                    <i class="fa-solid fa-cloud-sun text-slate-200 dark:text-slate-700 text-5xl mb-4"></i>
                    <p class="text-slate-400 dark:text-slate-500 font-medium text-sm italic">All caught up! No new notifications.</p>
                </div>
            </div>
        </div>

    </main>
</div>

</body>
</html>