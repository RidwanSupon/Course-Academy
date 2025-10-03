<?php
// Ensure session and admin check are run only once in your main page wrapper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check the current file name for highlighting
$current = basename($_SERVER['PHP_SELF']);

// Define the navigation links and their filenames
$nav_links = [
    'Dashboard' => 'index.php',
    'Banners' => 'banners.php',
    'Courses' => 'courses.php', // Assuming you have a courses page
    'Videos' => 'videos.php',
    'Gallery' => 'gallery.php',
    'Reviews' => 'reviews.php',
    'Enrollments' => 'enrollments.php',
    'Free Class Requests' => 'free_class_requests.php', // Added from your previous context
    'Mentors' => 'mentors.php',
    'Total-Enrolled' => 'EnrolledStudent.php',
    'Free-Class List' => 'FreeStudent.php',
];
?>

<aside class="w-64 bg-gray-800 text-gray-100 min-h-screen flex flex-col shadow-2xl">
    <div class="p-6 border-b border-gray-700 bg-blue-900">
        <h3 class="text-2xl font-extrabold tracking-wider text-white">
            Admin Panel
        </h3>
    </div>

    <nav class="flex flex-col p-4 space-y-1 flex-grow">
        <?php foreach ($nav_links as $label => $file): ?>
            <?php
                // Determine active state
                $isActive = $current === $file;
                $activeClasses = $isActive ? 'bg-blue-900 text-white font-semibold shadow-md border-r-4 border-yellow-400' : 'text-gray-300 hover:bg-gray-700 hover:text-white';
            ?>
            <a href="<?= htmlspecialchars($file) ?>" 
               class="px-4 py-2 rounded-lg transition duration-150 ease-in-out <?= $activeClasses ?>">
                <?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-gray-700">
        <a href="logout.php" 
           class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg transition duration-150">
            <span class="material-icons align-middle text-sm mr-1">logout</span>
            Logout
        </a>
    </div>
</aside>