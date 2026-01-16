<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";
allowRoles(['admin']);

$db = (new Database())->getConnection();

// Dashboard Statistics
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeUsers = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
// Example SQL logic (update based on your table structure)
$pendingComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetchColumn();
$inProgressComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'in progress'")->fetchColumn();
$resolvedComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'resolved'")->fetchColumn();
// Total complaints for the percentage calculation
$complaints = $pendingComplaints + $inProgressComplaints + $resolvedComplaints;
$todayComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$totalEvents = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$upcomingEvents = $db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();

// Recent activities
$recentComplaints = $db->query("
    SELECT c.*, u.name as user_name 
    FROM complaints c 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$recentUsers = $db->query("
    SELECT name, email, created_at, role 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Monthly user growth
$monthlyUsers = $db->query("
    SELECT 
        DATE_FORMAT(created_at, '%b %Y') as month,
        COUNT(*) as count
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY MIN(created_at) ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SmartCampus</title>
    
</head>
<body class=" bg-gray-50">

<?php include "../../includes/dashboard_sidebar.php"; ?>

<main class="sm:ml-64 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard Overview</h1>
                <p class="text-gray-600 text-sm mt-1">Welcome back, Administrator. Here's what's happening today.</p>
            </div>
          
        </div>
    </header>
 <div class="p-8">
   
       <!-- Stats Grid - Ultra Minimal -->
<div class="grid  grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Total Users Card -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Total Users</p>
                <h3 class="text-xl font-bold text-gray-800"><?= number_format($totalUsers) ?></h3>
            </div>
        </div>
    </div>

    <!-- Active Users Card -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-green-300 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center">
                <i class="fas fa-user-check text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Active Users</p>
                <h3 class="text-xl font-bold text-gray-800"><?= number_format($activeUsers) ?></h3>
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-500">
            <?= round(($activeUsers/$totalUsers)*100) ?>% active
        </div>
    </div>

    <!-- Complaints Card -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-orange-300 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Complaints</p>
                <h3 class="text-xl font-bold text-gray-800"><?= number_format($complaints) ?></h3>
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-500 flex items-center justify-between">
            <span><?= $pendingComplaints ?> pending</span>
            <?php if($pendingComplaints > 0): ?>
            <span class="text-orange-600 font-medium">!</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Events Card -->
    <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-purple-300 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-purple-600 text-sm"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium">Upcoming</p>
                <h3 class="text-xl font-bold text-gray-800"><?= number_format($upcomingEvents) ?></h3>
            </div>
        </div>
        <div class="mt-2 text-xs text-gray-500">
            <?= number_format($totalEvents) ?> total
        </div>
    </div>
</div>

        <!-- Charts & Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">User Growth</h3>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Last 6 Months</option>
                        <option>Last Year</option>
                        <option>All Time</option>
                    </select>
                </div>
                <div class="h-64">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>

            <!-- Complaints Overview -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Complaints Overview</h3>
        <span class="text-sm text-gray-500">Today: <?= $todayComplaints ?> new</span>
    </div>
    
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Pending</span>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-800 dark:text-white"><?= $pendingComplaints ?></span>
                <span class="text-sm text-gray-500 ml-1">complaints</span>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">In Progress</span>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-800 dark:text-white"><?= $inProgressComplaints ?></span>
                <span class="text-sm text-gray-500 ml-1">complaints</span>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Resolved</span>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-800 dark:text-white"><?= $resolvedComplaints ?></span>
                <span class="text-sm text-gray-500 ml-1">complaints</span>
            </div>
        </div>

        <div class="pt-4">
            <div class="flex h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="bg-red-500 transition-all duration-500" 
                     style="width: <?= $complaints > 0 ? ($pendingComplaints / $complaints) * 100 : 0 ?>%"></div>
                <div class="bg-yellow-500 transition-all duration-500" 
                     style="width: <?= $complaints > 0 ? ($inProgressComplaints / $complaints) * 100 : 0 ?>%"></div>
                <div class="bg-green-500 transition-all duration-500" 
                     style="width: <?= $complaints > 0 ? ($resolvedComplaints / $complaints) * 100 : 0 ?>%"></div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Recent Activities Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Complaints -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Complaints</h3>
                        <a href="complaints.php" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All →</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($recentComplaints as $complaint): ?>
                    <div class="p-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-800 truncate"><?= htmlspecialchars($complaint['category']) ?></h4>
                            <span class="text-xs px-2 py-1 rounded-full 
                                <?= $complaint['status'] == 'pending' ? 'bg-red-100 text-red-800' : 
                                   ($complaint['status'] == 'resolved' ? 'bg-green-100 text-green-800' : 
                                   'bg-yellow-100 text-yellow-800') ?>">
                                <?= ucfirst($complaint['status']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars($complaint['description']) ?></p>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                <?= htmlspecialchars($complaint['user_name']) ?>
                            </span>
                            <span class="text-gray-400">
                                <?= date('M d, H:i', strtotime($complaint['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($recentComplaints)): ?>
                    <div class="p-8 text-center">
                        <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent complaints</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- New Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">New Users</h3>
                        <a href="users.php" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All →</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="p-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-linear-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                <span class="text-blue-600 font-bold uppercase"><?= substr($user['name'], 0, 1) ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 truncate"><?= htmlspecialchars($user['name']) ?></h4>
                                <p class="text-sm text-gray-600 truncate"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    <?= $user['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 
                                       ($user['role'] == 'faculty' ? 'bg-blue-100 text-blue-800' : 
                                       'bg-gray-100 text-gray-800') ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?= date('M d', strtotime($user['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($recentUsers)): ?>
                    <div class="p-8 text-center">
                        <i class="fas fa-user-plus text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No new users</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="add_event.php" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition duration-200">
                            <i class="fas fa-calendar-plus text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Create Event</h4>
                            <p class="text-sm text-gray-500">Schedule new campus event</p>
                        </div>
                    </div>
                </a>
                
                <a href="notices.php" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-green-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition duration-200">
                            <i class="fas fa-bullhorn text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Post Notice</h4>
                            <p class="text-sm text-gray-500">Announce important updates</p>
                        </div>
                    </div>
                </a>
                
                <a href="reports.php" class="bg-white border border-gray-200 rounded-xl p-4 hover:border-purple-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition duration-200">
                            <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">Generate Reports</h4>
                            <p class="text-sm text-gray-500">View analytics & insights</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        </div>
</main>

<script>
// User Growth Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    
    <?php
    $labels = [];
    $data = [];
    foreach ($monthlyUsers as $monthly) {
        $labels[] = $monthly['month'];
        $data[] = $monthly['count'];
    }
    ?>
    
    const userGrowthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'New Users',
                data: <?= json_encode($data) ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 14
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 5
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Real-time update simulation
    setInterval(() => {
        const notificationCount = document.querySelector('.absolute .w-5');
        if (notificationCount) {
            const currentCount = parseInt(notificationCount.textContent);
            if (currentCount < 10) {
                notificationCount.textContent = currentCount + 1;
            }
        }
    }, 30000);
});
</script>


</body>
</html>