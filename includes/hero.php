<section  class="relative bg-white  dark:bg-gray-900 overflow-hidden">
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full opacity-10 dark:opacity-20 pointer-events-none">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[70%] bg-blue-400 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[70%] bg-indigo-400 rounded-full blur-[120px]"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-64px)] text-center py-20">
      
      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 mb-8 animate-fade-in">
        ✨ New: Academic Year 2026 Portal is Live
      </span>

      <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-6">
        Elevate Your <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-indigo-500">Campus</span>
      </h1>
      <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-6"><span class="text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-indigo-500">Experience</span></h1>

      <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-600 dark:text-gray-400 leading-relaxed mb-10">
        A unified digital ecosystem for students and faculty. Manage notices, resolve complaints, and track campus events—all in one smart dashboard.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
        
       <?php if (isset($_SESSION['user_id'])): ?>

    <a href="dashboard/<?=$_SESSION['role']?>/<?=$_SESSION['role']; ?>.php"
      class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 
              text-white rounded-xl font-bold text-lg shadow-lg 
              shadow-blue-500/30 transition-all hover:-translate-y-1">
        Dashboard
    </a>

<?php else: ?>

    <a href="/auth/register.php"
       class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 
              text-white rounded-xl font-bold text-lg shadow-lg 
              shadow-blue-500/30 transition-all hover:-translate-y-1">
        Get Started Now
    </a>

<?php endif; ?>

        <a href="#features" 
           class="w-full sm:w-auto px-8 py-4 bg-transparent border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
           View Features
        </a>
      </div>

      <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800 grid grid-cols-2 md:grid-cols-3 gap-8">
        <div>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">5k+</p>
          <p class="text-sm text-gray-500">Active Students</p>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">100%</p>
          <p class="text-sm text-gray-500">Digital Resolution</p>
        </div>
        <div class="col-span-2 md:col-span-1">
          <p class="text-2xl font-bold text-gray-900 dark:text-white">24/7</p>
          <p class="text-sm text-gray-500">System Uptime</p>
        </div>
      </div>

    </div>
  </div>
</section>