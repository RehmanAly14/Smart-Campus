<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /dashboard/{$_SESSION['role']}/{$_SESSION['role']}.php");
    exit;
}
// Retrieve errors and old input from session
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Smart Campus</title>
    <link rel="stylesheet" href="../styles/output.css">
    
    <script src="../assets/js/theme.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-950 font-sans antialiased">
    <?php include_once "../includes/header.php"; ?>

<div class="flex min-h-screen">
    
    <div class="hidden lg:flex w-1/2 rounded-r-3xl bg-indigo-700 p-12 flex-col justify-between text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 -ml-20 -mt-20 w-80 h-80 bg-indigo-500 rounded-full opacity-50"></div>
        <div class="absolute bottom-0 right-0 -mr-20 -mb-20 w-96 h-96 bg-blue-500 rounded-full opacity-30"></div>

        <div class="relative z-10">
            <a href="../index.php" class="text-3xl font-extrabold tracking-tight">
                Smart<span class="text-indigo-200">Campus</span>
            </a>
            
            <div class="mt-20">
                <h1 class="text-5xl font-bold leading-tight mb-6">Join the Digital <br>Campus Revolution</h1>
                <p class="text-indigo-100 text-lg max-w-md leading-relaxed">
                    Create your account today to start participating in events, raising concerns, and staying connected with your academic community.
                </p>
            </div>
        </div>

        <div class="relative z-10">
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center text-xl">🚀</div>
                    <p class="text-indigo-100">Fast and secure account setup</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center text-xl">🛡️</div>
                    <p class="text-indigo-100">Role-based access control</p>
                </div>
            </div>
            <p class="mt-12 text-sm text-indigo-200">© 2025 Smart Campus Utility Portal</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-white dark:bg-gray-900">
        <div class="w-full max-w-md">
            
            <div class="lg:hidden text-center mb-8">
                <span class="text-3xl font-extrabold text-indigo-600">Smart<span class="text-gray-900 dark:text-white">Campus</span></span>
            </div>

            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Create Account</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Join thousands of students and faculty members.</p>
                 
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

            <form action="register_process.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" placeholder="name@university.edu" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                </div>

                 <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
        <select name="role"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" required>
          <option value="">Select Role</option>
          <option value="student" <?= (isset($old['role']) && $old['role'] === 'student') ? 'selected' : '' ?>>Student</option>
          <option value="faculty" <?= (isset($old['role']) && $old['role'] === 'faculty') ? 'selected' : '' ?>>Faculty</option>
         
        </select>
      </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="••••••••" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1 active:scale-95">
                    Create Account
                </button>
            </form>

            <p class="mt-8 text-center text-gray-600 dark:text-gray-400">
                Already have an account? 
                <a href="login.php" class="text-indigo-600 font-bold hover:underline">Sign in instead</a>
            </p>
        </div>
    </div>

</div>
<?php include_once "../includes/footer.php"; ?>
</body>

</html>