<?php
session_start();
require_once __DIR__ . "/../../../config/db.php";

$database = new Database();
$conn = $database->getConnection();

// Get filters from URL
$status_filter = $_GET['status'] ?? 'all';
$category_filter = $_GET['category'] ?? 'all';


// Build SQL with filters
$sql = "
SELECT complaints.*, users.name, users.email
FROM complaints
JOIN users ON complaints.user_id = users.id
WHERE 1=1
";

$params = [];

if ($status_filter !== 'all') {
    $sql .= " AND complaints.status = :status";
    $params[':status'] = $status_filter;
}

if ($category_filter !== 'all') {
    $sql .= " AND complaints.category = :category";
    $params[':category'] = $category_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (complaints.subject LIKE :search OR complaints.description LIKE :search OR users.name LIKE :search)";
    $params[':search'] = "%$search_query%";
}

$sql .= " ORDER BY complaints.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats for filters
$stats_sql = "
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    GROUP_CONCAT(DISTINCT category) as categories
FROM complaints
";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
$categories = $stats['categories'] ? explode(',', $stats['categories']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints Management | SmartCampus</title>
   
</head>
<body class="bg-gray-50">

<?php include "../../../includes/dashboard_sidebar.php"; ?>

<div class=" sm:ml-64 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Complaints Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage and resolve student complaints</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="exportComplaints()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <button onclick="printComplaints()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    Print
                </button>
            </div>
        </div>
    </header>

    <main class="p-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                <div class="flex p-6 items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Complaints</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['total'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-inbox text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                <div class="flex p-6 items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['pending'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                <div class="flex p-6 items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">In Progress</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['in_progress'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center">
                        <i class="fas fa-tasks text-yellow-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                <div class="flex p-6 items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Resolved</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['resolved'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
               

                <!-- Status Filter -->
                <div>
                    <select name="status" 
                            onchange="window.location.href = updateUrlParam('status', this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                        <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $status_filter === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Resolved" <?= $status_filter === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category" 
                            onchange="window.location.href = updateUrlParam('category', this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                        <option value="all" <?= $category_filter === 'all' ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= $category_filter === $category ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            <?php if ($status_filter !== 'all' || $category_filter !== 'all' || !empty($search_query)): ?>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                <?php if ($status_filter !== 'all'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    Status: <?= $status_filter ?>
                    <button onclick="removeFilter('status')" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                <?php if ($category_filter !== 'all'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    Category: <?= $category_filter ?>
                    <button onclick="removeFilter('category')" class="ml-2 text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                <?php if (!empty($search_query)): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                    Search: "<?= htmlspecialchars($search_query) ?>"
                    <button onclick="removeFilter('search')" class="ml-2 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                <button onclick="clearAllFilters()" class="text-sm text-red-600 hover:text-red-800 font-medium ml-2">
                    Clear all
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Complaints Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    Complaints <span class="text-gray-500 font-normal">(<?= count($complaints) ?>)</span>
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">
                        Showing <?= count($complaints) ?> of <?= $stats['total'] ?>
                    </span>
                </div>
            </div>

            <?php if (empty($complaints)): ?>
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-700 mb-2">No complaints found</h4>
                <p class="text-gray-500 mb-6">
                    <?php if ($status_filter !== 'all' || $category_filter !== 'all' || !empty($search_query)): ?>
                        Try adjusting your filters or search query
                    <?php else: ?>
                        No complaints have been submitted yet
                    <?php endif; ?>
                </p>
                <?php if ($status_filter !== 'all' || $category_filter !== 'all' || !empty($search_query)): ?>
                <button onclick="clearAllFilters()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                    Clear Filters
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Student & Details</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Category</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Submitted</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($complaints as $row): 
                            $statusColor = $row['status'] === 'Pending' ? 'red' : 
                                         ($row['status'] === 'In Progress' ? 'yellow' : 'green');
                        ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6">
                                <div>
                                    <h4 class="font-medium text-gray-800"><?= htmlspecialchars($row['name']) ?></h4>
                                    <p class="text-sm text-gray-600 mb-1"><?= htmlspecialchars($row['email']) ?></p>
                                    <p class="text-sm text-gray-800 font-medium mb-1"><?= htmlspecialchars($row['category']) ?></p>
                                    <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars(substr($row['description'], 0, 150)) ?>...</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800">
                                    <?= htmlspecialchars($row['category']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-gray-800"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                                <p class="text-sm text-gray-500"><?= date('h:i A', strtotime($row['created_at'])) ?></p>
                            </td>
                            <td class="py-4 px-6">
                                <form action="update_status.php" method="POST" class="inline-flex" onchange="this.submit()">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <select name="status" 
                                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-sm font-medium focus:ring-2 focus:ring-<?= $statusColor ?>-500 focus:border-<?= $statusColor ?>-500 transition duration-150 appearance-none bg-white"
                                            style="color: <?= 
                                                $row['status'] === 'Pending' ? '#dc2626' : 
                                                ($row['status'] === 'In Progress' ? '#d97706' : '#059669')
                                            ?>">
                                        <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?> style="color: #dc2626">Pending</option>
                                        <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?> style="color: #d97706">In Progress</option>
                                        <option value="Resolved" <?= $row['status'] === 'Resolved' ? 'selected' : '' ?> style="color: #059669">Resolved</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                   
                                  
                                    <button onclick="deleteComplaint(<?= $row['id'] ?>)" 
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition duration-150"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                <div class="text-gray-600 text-sm">
                    Showing <span class="font-medium"><?= count($complaints) ?></span> complaints
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="px-3 py-2 text-sm font-medium text-gray-700">1</span>
                    <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
// URL parameter management
function updateUrlParam(param, value) {
    const url = new URL(window.location);
    if (value === 'all') {
        url.searchParams.delete(param);
    } else {
        url.searchParams.set(param, value);
    }
    return url.toString();
}

function removeFilter(param) {
    window.location.href = updateUrlParam(param, 'all');
}

function clearAllFilters() {
    window.location.href = window.location.pathname;
}




function deleteComplaint(id) {
    if (confirm('Are you sure you want to delete this complaint? This action cannot be undone.')) {
        window.location.href = `delete_complaint.php?id=${id}`;
    }
}

function exportComplaints() {
    // Add export functionality here
    alert('Export feature coming soon!');
}

function printComplaints() {
    window.print();
}

// Auto-submit search on enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
    
    // Update status color dynamically
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const colors = {
                'Pending': '#dc2626',
                'In Progress': '#d97706',
                'Resolved': '#059669'
            };
            this.style.color = colors[this.value] || '#374151';
        });
    });
});
</script>

</body>
</html>