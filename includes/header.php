<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="apple-touch-icon" sizes="180x180" href="/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="/favicon_io/site.webmanifest">
    
     <link rel="stylesheet" href="/styles/output.css">
    
<script src="/assets/js/theme.js" defer></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
(function () {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();
</script>

</head>
<body>
  


<nav x-data="{ mobileMenu: false, profileMenu: false }" class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-100 dark:border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">

      <div class="shrink-0">
        <a href="/" class="text-2xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400">
          Smart<span class="text-gray-900 dark:text-white">Campus</span>
        </a>
      </div>

      <div class="hidden md:flex items-center space-x-8">
        <a href="/" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</a>
        <a href="#about" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
        <a href="#features" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Features</a>
      </div>

      <div class="flex items-center space-x-4">

        <button onclick="toggleTheme()" class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
          <span class="dark:hidden">🌙</span>
          <span class="hidden dark:block">☀️</span>
        </button>

        <div class="relative">
          <button @click="profileMenu = !profileMenu" @click.away="profileMenu = false" class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800 hover:bg-blue-600 hover:text-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </button>

          <div x-show="profileMenu" 
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="transform opacity-0 scale-95"
               x-transition:enter-end="transform opacity-100 scale-100"
               class="absolute right-0 mt-3 w-56 origin-top-right bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden" 
               style="display: none;">
            
            <div class="py-2">
              <?php if (isset($_SESSION['role'])): ?>
                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700 mb-1">
                  <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Signed in as</p>
                  <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($_SESSION['name'] ?? 'User Account') ?></p>
                </div>
                <a href="dashboard/<?=$_SESSION['role']?>/<?=$_SESSION['role']?>.php" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                   Dashboard
                </a>
                <a href="auth/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                   Logout
                </a>
              <?php else: ?>
                <a href="auth/login.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700/50">Login</a>
                <div class="px-2 pt-1">
                    <a href="auth/register.php" class="block w-full text-center px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                        Create Account
                    </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
          <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
          <svg x-show="mobileMenu" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
    </div>
  </div>

  <div x-show="mobileMenu" x-collapse class="md:hidden bg-white dark:bg-gray-900 border-t dark:border-gray-800">
    <div class="px-4 pt-2 pb-6 space-y-1">
      <a href="/" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Home</a>
      <a href="#about" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">About</a>
      <a href="#features" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Features</a>
    </div>
  </div>
</nav>
</body>
</html>