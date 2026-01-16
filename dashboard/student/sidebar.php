<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['name'] ?? 'Student';
$role = $_SESSION['role'] ?? 'student'; // Logic kept
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="apple-touch-icon" sizes="180x180" href="../../favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon_io/favicon-16x16.png">
    <link rel="manifest" href="../../favicon_io/site.webmanifest">
     <link rel="stylesheet" href="/styles/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    

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

    <nav class="flex-1 px-4 space-y-1 mt-4 overflow-y-auto">
        <?php
        $menuItems = [
            ['name' => 'Dashboard', 'file' => 'student.php', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['name' => 'Events', 'file' => 'event_list.php', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['name' => 'Complaints', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2', 'file' => 'complaints.php'],
            ['name' => 'Notices', 'file' => 'notices.php', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
        ];

        foreach ($menuItems as $item):
            $isActive = ($current_page == $item['file']);
        ?>
            <a href="<?= $item['file'] ?>" 
               class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?= $isActive ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
                
                <svg class="w-5 h-5 <?= $isActive ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' ?>" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?= $item['icon'] ?>" />
                </svg>
                
                <span class="ml-3 text-sm font-medium"><?= $item['name'] ?></span>
                
                <?php if($isActive): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 absolute bottom-0 w-full border-t border-gray-800 bg-gray-900/50">
        <div class="flex items-center p-2 mb-2">
            <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600 uppercase">
                <?= substr($user_name, 0, 1) ?>
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-xs font-bold text-white leading-none truncate capitalize"><?= htmlspecialchars($user_name) ?></p>
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider font-medium">STUDENT</p>
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