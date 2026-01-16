<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['name'] ?? 'admin';
$role = $_SESSION['role'] ?? 'Administrater'; // Logic kept
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Portal</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/favicon_io/site.webmanifest">
 

    <!-- Stylesheets -->
    <link rel="stylesheet" href="/styles/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    


<!-- TOGGLE BUTTON (MOBILE ONLY) -->
<button
    id="sidebarToggle"
    class="fixed top-4 left-4 z-50 p-2 rounded-lg
           bg-gray-900 text-white
           sm:hidden">
    ☰
</button>

<aside
    id="sidebar"
    class="fixed top-0 left-0 z-40 h-screen w-64
           bg-gray-900 text-white
           transform -translate-x-full
           sm:translate-x-0
           transition-transform duration-300">

    <!-- LOGO -->
    <div class="p-6">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-600/20">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <a href="/" class="text-white font-bold text-lg tracking-tight">Smart<span class="text-blue-500">Portal</span></a>
        </div>
    </div>

    <!-- MENU -->
    <nav class="px-4 space-y-1 mt-4">
        <?php
        $menuItems = [
            ['Dashboard','admin.php'],
            ['Users','users/users_list.php'],
            ['Notices','notices/notice.php'],
            ['Events','event.php'],
            ['Complaints','complaints/complaints.php'],
        ];

        foreach ($menuItems as [$name, $file]):
            $active = strpos($_SERVER['PHP_SELF'], $file) !== false;
        ?>
            <a href="/dashboard/<?= $role ?>/<?= $file ?>"
               class="block px-4 py-3 rounded-xl text-sm font-medium
               <?= $active ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
               <?= $name ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- USER -->
    <div class="absolute bottom-0 w-full p-4 border-t border-gray-800">
        <div class="flex items-center p-2 mb-2">
            <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600 uppercase">
                <?= substr($user_name, 0, 1) ?>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-xs font-bold text-white leading-none truncate capitalize"><?= htmlspecialchars($user_name) ?></p>
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider font-medium"><?= $role ?></p>
            </div>
        </div>
       

        <a href="/auth/logout.php" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-red-400 hover:bg-red-500/10 transition-all text-sm font-bold group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- JS -->
<script>
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
</script>
</body>
</html>