<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
allowRoles(['admin']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event | SmartCampus</title>
    
</head>
<body class="bg-gray-50 ">

<?php include "../../includes/dashboard_sidebar.php"; ?>

<div class=" sm:ml-64 min-h-screen">
    <header class="bg-white  px-10 py-6 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800 tracking-tight uppercase">Create New Event</h1>
        <a href="event.php" class="text-gray-500 hover:text-black transition-all text-sm font-bold uppercase tracking-widest">
            <i class="fas fa-arrow-left mr-2"></i> Cancel
        </a>
    </header>

    <main class="p-8">
        <div class="max-w-3xl mx-auto">
            <form action="event_process.php" method="POST" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-8 space-y-6">
                    
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Event Title</label>
                        <input type="text" name="title" required placeholder="e.g. Tech Symposium 2025" 
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Description</label>
                        <textarea name="description" rows="4" required placeholder="Details about the event..."
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Date</label>
                            <input type="date" name="event_date" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Time</label>
                            <input type="time" name="event_time" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Location</label>
                        <input type="text" name="location" required placeholder="e.g. Conference Hall B" 
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Event Category</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <?php 
                            $types = ['Academic', 'Sports', 'Cultural', 'Workshop', 'Seminar', 'Other'];
                            foreach($types as $type): ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="event_type" value="<?= $type ?>" class="sr-only peer" <?= $type == 'Academic' ? 'checked' : '' ?>>
                                <div class="px-3 py-3 text-center rounded-xl border border-gray-100 bg-gray-50 text-xs font-bold text-gray-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all">
                                    <?= $type ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="px-10 py-8  flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-600/20 transition-all active:scale-95">
                        Publish Event
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>