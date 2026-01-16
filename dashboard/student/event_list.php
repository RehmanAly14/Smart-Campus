<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['student']);

$db = (new Database())->getConnection();
$studentId = $_SESSION['user_id'];

// Logic remains untouched
$query = "
SELECT e.*,
       (SELECT COUNT(*) 
        FROM event_registrations er 
        WHERE er.event_id = e.id 
          AND er.student_id = :sid) AS is_registered
FROM events e
ORDER BY e.event_date ASC
";
$stmt = $db->prepare($query);
$stmt->execute(['sid' => $studentId]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events | Smart Portal</title>
   
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .event-card {
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .event-card:hover {
            transform: translateY(-2px);
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-[#fcfcfd] dark:bg-slate-900 text-slate-800 dark:text-slate-200">

<div class=" min-h-screen">
    <?php include "sidebar.php"; ?>

    <main class="p-8  sm:ml-64 transition-all duration-300">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Campus Events</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">Find your next opportunity to learn and connect.</p>
            </div>
            <div class="px-4 py-4 text-center bg-blue-600 text-white rounded-2xl text-xs font-bold uppercase  shadow-lg shadow-blue-600/20">
                Upcoming Feed
            </div>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-24 bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-calendar-day text-5xl text-slate-100 dark:text-slate-700 mb-4"></i>
                <p class="text-slate-500 font-medium tracking-tight">No upcoming events found in the schedule.</p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <?php foreach ($events as $event): ?>
    <div class="event-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-all duration-300">
        
        <!-- Event Header with Date Badge -->
        <div class="p-6 border-b border-slate-50 dark:border-slate-700/50">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded-full">
                            <?= htmlspecialchars($event['event_type']) ?>
                        </span>
                        <span class="text-xs text-slate-400">
                            <?= date('M d, Y', strtotime($event['event_date'])) ?>
                        </span>
                    </div>
                    
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3 line-clamp-2">
                        <?= htmlspecialchars($event['title']) ?>
                    </h2>
                    
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed line-clamp-3 mb-4">
                        <?= htmlspecialchars($event['description']) ?>
                    </p>
                </div>
                
                <!-- Date Card -->
                <div class="text-center bg-slate-50 dark:bg-slate-900/50 rounded-xl p-3 min-w-[70px] border border-slate-100 dark:border-slate-700">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">
                        <?= date('d', strtotime($event['event_date'])) ?>
                    </div>
                    <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">
                        <?= date('M', strtotime($event['event_date'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Details -->
        <div class="p-6 pt-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="space-y-3">
                    <!-- Time -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-regular fa-clock text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Time</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                <?= $event['event_time'] ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-rose-100 dark:bg-rose-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-location-dot text-rose-600 dark:text-rose-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Location</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                <?= htmlspecialchars($event['location']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end">
                    <?php if ($event['is_registered'] > 0): ?>
                        <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl font-semibold text-sm">
                            <i class="fa-solid fa-check-circle"></i>
                            Registered
                        </div>
                    <?php else: ?>
                        <form action="register_event.php" method="POST" class="m-0">
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 hover:shadow-lg active:scale-95">
                                <i class="fa-solid fa-calendar-plus"></i>
                                Register Now
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
    <?php endforeach; ?>
</div>

        </div>
    </main>
</div>

</body>
</html>