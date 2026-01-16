<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | Smart Campus</title>
    <link rel="stylesheet" href="/styles/output.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full text-center">
        <div class="relative mb-8">
            <div class="absolute inset-0 bg-red-100 dark:bg-red-900/20 rounded-full blur-3xl scale-150 opacity-50"></div>
            <div class="relative flex justify-center">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-8xl font-black text-gray-200 dark:text-gray-800 mb-2">403</h1>
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Access Denied</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
            Sorry, you don't have the required permissions to view this section. This might be because your account role doesn't allow access here.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="javascript:history.back()" 
               class="px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-xl font-bold transition-all hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95">
               Go Back
            </a>
            <a href="/" 
               class="px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-xl font-bold transition-all hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95">
               Return Home
            </a>
        </div>

        <p class="mt-12 text-sm text-gray-400">
            Think this is a mistake? <a href="#" class="text-blue-600 hover:underline">Contact Administrator</a>
        </p>
    </div>

</body>
</html>
