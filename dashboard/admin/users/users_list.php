<?php

require_once "../../../auth/auth_check.php";
require_once "../../../auth/role_check.php";
require_once "../../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();


$role_filter = $_GET['role'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';


$sql = "SELECT id, name, email, role, is_active, created_at FROM users WHERE 1=1";
$params = [];

if ($role_filter !== 'all') {
    $sql .= " AND role = :role";
    $params[':role'] = $role_filter;
}

if ($status_filter !== 'all') {
    $is_active = $status_filter === 'active' ? 1 : 0;
    $sql .= " AND is_active = :is_active";
    $params[':is_active'] = $is_active;
}

if (!empty($search_query)) {
    $sql .= " AND (name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search_query%";
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $error = "Database error: " . $e->getMessage();
}

// Get stats based on your schema
try {
    $stats_sql = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
        COUNT(DISTINCT role) as roles_count,
        GROUP_CONCAT(DISTINCT role) as roles
    FROM users
    ";
    $stats_stmt = $db->prepare($stats_sql);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get unique roles from database
    $roles = $stats['roles'] ? explode(',', $stats['roles']) : [];
} catch (PDOException $e) {
    $stats = ['total' => 0, 'active' => 0, 'inactive' => 0, 'roles_count' => 0, 'roles' => ''];
    $roles = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | SmartCampus Admin</title>
    <link rel="stylesheet" href="../../../styles/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<?php include "../../../includes/dashboard_sidebar.php"; ?>

<div class=" sm:ml-64 transition-all duration-300">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">User Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage all user accounts and permissions</p>
            </div>
            
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

        <!-- Success Message -->
        <?php if(isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <p class="text-green-700"><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <!-- Error Message -->
        <?php if(isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-red-700"><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= number_format($stats['total'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Active Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= number_format($stats['active'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Inactive Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= number_format($stats['inactive'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-user-slash text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Roles</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= number_format($stats['roles_count'] ?? 0) ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fas fa-user-tag text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <form method="GET" class="w-full">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search by name or email..." 
                                   value="<?= htmlspecialchars($search_query) ?>"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </form>
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <select name="role" 
                            onchange="window.location.href = updateUrlParam('role', this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                        <option value="all" <?= $role_filter === 'all' ? 'selected' : '' ?>>All Roles</option>
                        <?php if(!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role) ?>" <?= $role_filter === $role ? 'selected' : '' ?>>
                                    <?= ucfirst($role) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="admin">Admin</option>
                            <option value="faculty">Faculty</option>
                            <option value="student">Student</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" 
                            onchange="window.location.href = updateUrlParam('status', this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button onclick="clearAllFilters()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-filter-circle-xmark"></i>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Active Filters -->
            <?php if ($role_filter !== 'all' || $status_filter !== 'all' || !empty($search_query)): ?>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                <?php if ($role_filter !== 'all'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    Role: <?= ucfirst($role_filter) ?>
                    <button onclick="removeFilter('role')" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                <?php endif; ?>
                <?php if ($status_filter !== 'all'): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    Status: <?= ucfirst($status_filter) ?>
                    <button onclick="removeFilter('status')" class="ml-2 text-green-600 hover:text-green-800">
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

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    Users <span class="text-gray-500 font-normal">(<?= count($users) ?>)</span>
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">
                        Showing <?= count($users) ?> of <?= $stats['total'] ?? 0 ?>
                    </span>
                </div>
            </div>

            <?php if (empty($users)): ?>
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-700 mb-2">No users found</h4>
                <p class="text-gray-500 mb-6">
                    <?php if ($role_filter !== 'all' || $status_filter !== 'all' || !empty($search_query)): ?>
                        Try adjusting your filters or search query
                    <?php else: ?>
                        No users registered yet
                    <?php endif; ?>
                </p>
                <?php if ($role_filter !== 'all' || $status_filter !== 'all' || !empty($search_query)): ?>
                <button onclick="clearAllFilters()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                    Clear Filters
                </button>
                <?php else: ?>
                <a href="add_user.php" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200 inline-flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    Add First User
                </a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">ID</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">User</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Email</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Role</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Joined</th>
                            <th class="text-left py-4 px-6 font-medium text-gray-700 text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($users as $user): 
                            // Role color mapping
                            $roleColor = $user['role'] === 'admin' ? 'purple' : 
                                       ($user['role'] === 'faculty' ? 'blue' : 'green');
                            
                            // Format date
                            $created_at = date('M d, Y', strtotime($user['created_at']));
                            $created_time = date('h:i A', strtotime($user['created_at']));
                        ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6">
                                <span class="text-gray-600 font-mono">#<?= $user['id'] ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-linear-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold uppercase"><?= substr($user['name'], 0, 1) ?></span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?= htmlspecialchars($user['name']) ?></h4>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-gray-800">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    <?= htmlspecialchars($user['email']) ?>
                                </p>
                            </td>
                            <td class="py-4 px-6">
                                <form action="update_user.php" method="POST" class="inline-flex" onchange="this.submit()">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="action" value="update_role">
                                    <select name="role" 
                                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-sm font-medium focus:ring-2 focus:ring-<?= $roleColor ?>-500 focus:border-<?= $roleColor ?>-500 transition duration-150 appearance-none bg-white"
                                            style="color: <?= 
                                                $user['role'] === 'admin' ? '#7c3aed' : 
                                                ($user['role'] === 'faculty' ? '#2563eb' : '#059669')
                                            ?>">
                                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?> style="color: #059669">Student</option>
                                        <option value="faculty" <?= $user['role'] === 'faculty' ? 'selected' : '' ?> style="color: #2563eb">Faculty</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?> style="color: #7c3aed">Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <form action="update_user.php" method="POST" class="inline-flex">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-full text-sm font-medium transition duration-150 flex items-center gap-2 <?= $user['is_active'] ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?>">
                                        <?php if($user['is_active']): ?>
                                            <i class="fas fa-check-circle text-green-600"></i>
                                            Active
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-gray-600"></i>
                                            Inactive
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-gray-800"><?= $created_at ?></span>
                                <p class="text-sm text-gray-500"><?= $created_time ?></p>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" 
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition duration-150"
                                            title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button onclick="resetPassword(<?= $user['id'] ?>)" 
                                            class="p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition duration-150"
                                            title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-500 italic">Current user</span>
                                    <?php endif; ?>
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
                    Showing <span class="font-medium"><?= count($users) ?></span> users
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

// Action functions
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = `update_user.php?action=delete&id=${id}`;
    }
}

function resetPassword(id) {
    if (confirm('Reset password for this user? A new random password will be generated.')) {
        window.location.href = `update_user.php?action=reset_password&id=${id}`;
    }
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
    
    // Update role color dynamically
    const roleSelects = document.querySelectorAll('select[name="role"]');
    roleSelects.forEach(select => {
        select.addEventListener('change', function() {
            const colors = {
                'admin': '#7c3aed',
                'faculty': '#2563eb',
                'student': '#059669'
            };
            this.style.color = colors[this.value] || '#374151';
        });
    });
});
</script>

</body>
</html>