<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();

$events = $db->query("
    SELECT e.*, u.name AS admin_name
    FROM events e
    JOIN users u ON e.created_by = u.id
    ORDER BY event_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events Management</title>
   
</head>
<body class=" bg-gray-50">

<?php include "../../includes/dashboard_sidebar.php"; ?>

<div class="sm:ml-64 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white shadow-sm ">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Events Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage and organize all events</p>
            </div>
            <a href="add_event.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition duration-200">
                <i class="fas fa-plus"></i>
                Add New Event
            </a>
        </div>
    </header>

    <main class="p-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Events</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2"><?= count($events) ?></h3>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Upcoming Events</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">
                            <?= count(array_filter($events, function($event) {
                                return strtotime($event['event_date']) >= time();
                            })) ?>
                        </h3>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Past Events</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">
                            <?= count(array_filter($events, function($event) {
                                return strtotime($event['event_date']) < time();
                            })) ?>
                        </h3>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <i class="fas fa-history text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">
                            <?= count(array_filter($events, function($event) {
                                $eventMonth = date('m Y', strtotime($event['event_date']));
                                $currentMonth = date('m Y');
                                return $eventMonth === $currentMonth;
                            })) ?>
                        </h3>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <i class="fas fa-star text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">All Events</h2>
                <p class="text-gray-600 text-sm mt-1">List of all created events with details</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Event Details</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Date & Time</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Created By</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($events as $event): 
                            $eventDate = strtotime($event['event_date']);
                            $isPast = $eventDate < time();
                            $isToday = date('Y-m-d', $eventDate) === date('Y-m-d');
                            $isUpcoming = $eventDate > time();
                        ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6">
                                <div class="flex items-start gap-3">
                                    <div class="bg-blue-50 p-2 rounded-lg">
                                        <i class="fas fa-calendar text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?= htmlspecialchars($event['title']) ?></h4>
                                        <?php if(!empty($event['description'])): ?>
                                        <p class="text-gray-600 text-sm mt-1 line-clamp-2"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800">
                                        <?= date('M d, Y', $eventDate) ?>
                                    </span>
                                    <span class="text-gray-600 text-sm mt-1">
                                        <?= date('h:i A', $eventDate) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($event['admin_name']) ?></span>
                                </div>
                                <span class="text-gray-500 text-sm block mt-1">
                                    <?= date('M d, Y', strtotime($event['created_at'])) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <?php if($isToday): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    Today
                                </span>
                                <?php elseif($isPast): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Completed
                                </span>
                                <?php elseif($isUpcoming): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-calendar-alt mr-1.5"></i>
                                    Upcoming
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center ">
                                   
                                    <a href="delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Are you sure you want to delete this event?')" class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition duration-150" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($events)): ?>
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-700 mb-2">No events found</h3>
                                    <p class="text-gray-500 mb-6">Get started by creating your first event</p>
                                    <a href="add_event.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium inline-flex items-center gap-2 transition duration-200">
                                        <i class="fas fa-plus"></i>
                                        Create Event
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(!empty($events)): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                <div class="text-gray-600 text-sm">
                    Showing <span class="font-medium"><?= count($events) ?></span> events
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="px-4 py-2 text-sm font-medium text-gray-700">1</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Events</h3>
                <div class="space-y-4">
                    <?php 
                    $recentEvents = array_slice($events, 0, 3);
                    foreach($recentEvents as $recent): 
                    ?>
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-50 p-2 rounded-lg">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800"><?= htmlspecialchars($recent['title']) ?></h4>
                                <p class="text-gray-500 text-sm"><?= date('M d, Y', strtotime($recent['event_date'])) ?></p>
                            </div>
                        </div>
                        <span class="text-gray-500 text-sm">
                            by <?= htmlspecialchars($recent['admin_name']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Soon</h3>
                <div class="space-y-4">
                    <?php 
                    $upcomingEvents = array_filter($events, function($event) {
                        return strtotime($event['event_date']) >= time();
                    });
                    $upcomingEvents = array_slice($upcomingEvents, 0, 3);
                    
                    if(empty($upcomingEvents)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-day text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No upcoming events</p>
                        </div>
                    <?php else: 
                        foreach($upcomingEvents as $upcoming): 
                    ?>
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-50 p-2 rounded-lg">
                                <i class="fas fa-clock text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800"><?= htmlspecialchars($upcoming['title']) ?></h4>
                                <p class="text-gray-500 text-sm">
                                    In <?= floor((strtotime($upcoming['event_date']) - time()) / (60 * 60 * 24)) ?> days
                                </p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm font-medium">
                            <?= date('M d', strtotime($upcoming['event_date'])) ?>
                        </span>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>