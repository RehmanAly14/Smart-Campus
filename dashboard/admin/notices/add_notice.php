<?php
require_once "../../../auth/auth_check.php";
require_once "../../../auth/role_check.php";
require_once "../../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $posted_by = $_SESSION['user_id'];
    $targets = $_POST['targets'] ?? ['all'];
    
    // Validation
    if (empty($title) || empty($description)) {
        $error = "Title and description are required";
    } elseif (strlen($title) > 200) {
        $error = "Title must be less than 200 characters";
    } elseif (strlen($description) > 5000) {
        $error = "Description must be less than 5000 characters";
    } else {
        try {
            // Insert notice
            $stmt = $db->prepare("INSERT INTO notices (title, description, posted_by, status, created_at) 
                                  VALUES (:title, :description, :posted_by, 'published', NOW())");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':posted_by' => $posted_by
            ]);
            
            $notice_id = $db->lastInsertId();
            $success = "Notice published successfully!";
            
            // Clear form
            $_POST = [];
            
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Notice | SmartCampus Admin</title>
   
</head>
<body class="bg-gray-50">

<?php include "../../../includes/dashboard_sidebar.php"; ?>

<div class="sm:ml-64 transition-all duration-300">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex justify-between items-center px-8 py-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Create New Notice</h1>
                <p class="text-gray-600 text-sm mt-1">Share important announcements with the campus community</p>
            </div>
            <a href="notice.php" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-200">
                <i class="fas fa-arrow-left"></i>
                Back to Notices
            </a>
        </div>
    </header>

    <main class="p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Messages -->
            <?php if($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <p class="text-red-700"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <p class="text-green-700"><?= htmlspecialchars($success) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form method="POST" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notice Title <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 float-right" id="title-count">0/200</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               required 
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                               maxlength="200"
                               oninput="updateCharCount(this, 'title-count')"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               placeholder="Enter notice title (e.g., Exam Schedule Update)">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notice Description <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 float-right" id="desc-count">0/5000</span>
                        </label>
                        <textarea name="description" 
                                  rows="8"
                                  required
                                  maxlength="5000"
                                  oninput="updateCharCount(this, 'desc-count')"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                                  placeholder="Enter detailed notice description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <p class="text-xs text-gray-500 mt-2">You can use basic HTML tags for formatting</p>
                    </div>

                    <!-- Target Audience -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Target Audience
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="relative flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="checkbox" name="targets[]" value="all" checked class="sr-only peer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center peer-checked:bg-blue-100">
                                        <i class="fas fa-users text-gray-600 peer-checked:text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">All Users</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 border-2 border-gray-300 rounded peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 has-checked:border-green-500 has-checked:bg-green-50">
                                <input type="checkbox" name="targets[]" value="students" class="sr-only peer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center peer-checked:bg-green-100">
                                        <i class="fas fa-graduation-cap text-gray-600 peer-checked:text-green-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Students</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 border-2 border-gray-300 rounded peer-checked:border-green-500 peer-checked:bg-green-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 has-checked:border-purple-500 has-checked:bg-purple-50">
                                <input type="checkbox" name="targets[]" value="faculty" class="sr-only peer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center peer-checked:bg-purple-100">
                                        <i class="fas fa-chalkboard-teacher text-gray-600 peer-checked:text-purple-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Faculty</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 border-2 border-gray-300 rounded peer-checked:border-purple-500 peer-checked:bg-purple-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 has-checked:border-orange-500 has-checked:bg-orange-50">
                                <input type="checkbox" name="targets[]" value="staff" class="sr-only peer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center peer-checked:bg-orange-100">
                                        <i class="fas fa-user-tie text-gray-600 peer-checked:text-orange-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Staff</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 border-2 border-gray-300 rounded peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Priority Level
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="priority" value="normal" checked class="text-blue-600 focus:ring-blue-500">
                                <span class="text-gray-700">Normal</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="priority" value="important" class="text-orange-600 focus:ring-orange-500">
                                <span class="text-gray-700">Important</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="priority" value="urgent" class="text-red-600 focus:ring-red-500">
                                <span class="text-gray-700">Urgent</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex gap-3">
                            <button type="submit" name="action" value="publish" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Publish Now
                            </button>
                            <button type="submit" name="action" value="draft" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200 flex items-center gap-2">
                                <i class="far fa-save"></i>
                                Save as Draft
                            </button>
                        </div>
                        <a href="notices.php" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tips -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-blue-600 mt-0.5"></i>
                    <div>
                        <h4 class="font-medium text-blue-800 mb-1">Writing Effective Notices</h4>
                        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                            <li>Keep titles clear and concise</li>
                            <li>Include all relevant details in the description</li>
                            <li>Use appropriate target audience selection</li>
                            <li>Set priority level based on importance</li>
                            <li>Review before publishing to ensure accuracy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Character counter
function updateCharCount(element, counterId) {
    const counter = document.getElementById(counterId);
    const maxLength = counterId === 'title-count' ? 200 : 5000;
    const currentLength = element.value.length;
    counter.textContent = `${currentLength}/${maxLength}`;
    
    if (currentLength > maxLength * 0.9) {
        counter.classList.add('text-red-500');
        counter.classList.remove('text-gray-500');
    } else {
        counter.classList.remove('text-red-500');
        counter.classList.add('text-gray-500');
    }
}

// Initialize counters
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="title"]');
    const descInput = document.querySelector('textarea[name="description"]');
    if (titleInput) updateCharCount(titleInput, 'title-count');
    if (descInput) updateCharCount(descInput, 'desc-count');
});

// Auto uncheck "all" when specific targets are selected
document.querySelectorAll('input[name="targets[]"]').forEach(input => {
    input.addEventListener('change', function() {
        const allCheckbox = document.querySelector('input[value="all"]');
        const specificCheckboxes = document.querySelectorAll('input[name="targets[]"]:not([value="all"])');
        
        if (this.value !== 'all' && this.checked) {
            allCheckbox.checked = false;
        }
        
        // Check if all specific checkboxes are unchecked, then check "all"
        const anySpecificChecked = Array.from(specificCheckboxes).some(cb => cb.checked);
        if (!anySpecificChecked) {
            allCheckbox.checked = true;
        }
    });
});
</script>

</body>
</html>