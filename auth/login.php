<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /dashboard/{$_SESSION['role']}/{$_SESSION['role']}.php");
    exit;
}
// Retrieve errors and old input from session
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

// Clear session errors/old after use
unset($_SESSION['errors'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Campus</title>
    <link rel="stylesheet" href="/styles/output.css">
    <script src="../assets/js/theme.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-950 font-sans antialiased">
     <?php include_once "../includes/header.php"; ?>
  

<div class="flex min-h-screen">
    
<div class="hidden lg:flex rounded-r-4xl w-1/2 bg-blue-700 p-12 flex-col justify-between text-white relative overflow-hidden">

        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-500 rounded-full opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-indigo-500 rounded-full opacity-50"></div>

        <div class="relative z-10">
            <a href="index.php" class="text-3xl font-extrabold tracking-tight">
                Smart<span class="text-blue-200">Campus</span>
            </a>
            
            <div class="mt-20">
                <h1 class="text-5xl font-bold leading-tight mb-6">Your Unified <br>Campus Portal</h1>
                <p class="text-blue-100 text-lg max-w-md leading-relaxed">
                    Access your personalized dashboard to manage notices, resolve complaints, and track campus events in real-time.
                </p>
            </div>
        </div>

        <div class="relative z-10">
            <h3 class="font-semibold text-sm uppercase tracking-widest text-blue-200 mb-4">Available Roles</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/20 text-center">
                    <span class="block text-xl">🎓</span>
                    <p class="text-xs font-medium">Student</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/20 text-center">
                    <span class="block text-xl">👨‍🏫</span>
                    <p class="text-xs font-medium">Faculty</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/20 text-center">
                    <span class="block text-xl">⚙️</span>
                    <p class="text-xs font-medium">Admin</p>
                </div>
            </div>
            <p class="mt-12 text-sm text-blue-200">© 2025 Smart Campus Utility Portal</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-16 bg-white dark:bg-gray-900">
        <div class="w-full max-w-md">
            
            <div class="lg:hidden text-center mb-8">
                <span class="text-3xl font-extrabold text-blue-600">Smart<span class="text-gray-900 dark:text-white">Campus</span></span>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Please enter your details to sign in.</p>
            
            </div>
            <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-300 rounded-lg">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

            <form action="login_process.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="name@campus.edu" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" required>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                       
                    </div>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" required>
                </div>

               

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1 active:scale-95">
                    Sign In
                </button>
            </form>

            <p class="mt-8 text-center text-gray-600 dark:text-gray-400">
                Don't have an account? 
                <a href="register.php" class="text-blue-600 font-bold hover:underline">Sign up for free</a>
            </p>
        </div>
    </div>

</div>
<?php include_once "../includes/footer.php"; ?>
</body>

</html>