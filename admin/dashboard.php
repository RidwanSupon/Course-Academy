<?php
// admin/index.php
// Ensure session and admin check are run only once in your main page wrapper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Using require_admin() assumes it handles the session check/redirect
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Ensure $pdo is available, presumably from includes/functions.php or a config file it includes.
global $pdo;

// --- Fetch Dashboard Counts ---
try {
    // Standard Content Counts
    $counts['banners'] = $pdo->query("SELECT COUNT(*) FROM banners")->fetchColumn();
    $counts['courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $counts['videos'] = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
    $counts['gallery'] = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $counts['mentors'] = $pdo->query("SELECT COUNT(*) FROM mentors")->fetchColumn(); // Added mentor count

    // Action/Attention Counts
    $counts['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status='Pending'")->fetchColumn();
    $counts['free_requests'] = $pdo->query("SELECT COUNT(*) FROM free_class_requests WHERE status='New'")->fetchColumn();
    $counts['total_enrolled'] = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status='Approved'")->fetchColumn(); // Added Total Enrolled count
} catch (PDOException $e) {
    // Failsafe for database errors
    error_log("Dashboard count error: " . $e->getMessage());
    $counts = array_fill_keys(['banners', 'courses', 'videos', 'gallery', 'mentors', 'enrollments', 'free_requests', 'total_enrolled'], 'N/A');
}

// Define the dashboard cards structure for easy rendering
$dashboard_cards = [
    // --- Priority/Action Cards ---
    [
        'label' => 'Pending Enrollments',
        'count' => $counts['enrollments'],
        'icon' => 'notification_important',
        'color' => 'yellow',
        'link' => 'enrollments.php'
    ],
    [
        'label' => 'New Free Class Requests',
        'count' => $counts['free_requests'],
        'icon' => 'event_note',
        'color' => 'red',
        'link' => 'free_class_requests.php'
    ],
    // --- Data Cards (Revenue/Success Metrics) ---
    [
        'label' => 'Total Enrolled Students',
        'count' => $counts['total_enrolled'],
        'icon' => 'school',
        'color' => 'green',
        'link' => 'EnrolledStudent.php'
    ],
    // --- Content Management Cards ---
    [
        'label' => 'Active Courses',
        'count' => $counts['courses'],
        'icon' => 'book',
        'color' => 'indigo',
        'link' => 'courses.php'
    ],
    [
        'label' => 'Total Videos',
        'count' => $counts['videos'],
        'icon' => 'movie',
        'color' => 'blue',
        'link' => 'videos.php'
    ],
    [
        'label' => 'Gallery Items',
        'count' => $counts['gallery'],
        'icon' => 'collections',
        'color' => 'pink',
        'link' => 'gallery.php'
    ],
    [
        'label' => 'Mentors',
        'count' => $counts['mentors'],
        'icon' => 'people_alt',
        'color' => 'purple',
        'link' => 'mentors.php'
    ],
    [
        'label' => 'Active Banners',
        'count' => $counts['banners'],
        'icon' => 'image_search',
        'color' => 'teal',
        'link' => 'banners.php'
    ],
];
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">

    <div class="flex">
        <?php include __DIR__ . '/header_admin.php'; ?>

        <main class="flex-1 p-8">

            <div class="bg-white p-6 rounded-lg shadow-xl mb-8 border-b-4 border-indigo-600">
                <h1 class="text-3xl font-extrabold text-gray-800 flex items-center">
                    <span class="material-icons text-4xl text-indigo-600 mr-3">admin_panel_settings</span>
                    System Overview
                </h1>
                <p class="text-gray-500 mt-2">Quick summary of site activity and content statistics.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($dashboard_cards as $card): ?>
                    <?php
                    $color = $card['color'];
                    $border = "border-{$color}-600";
                    $icon_bg = "bg-{$color}-100";
                    $icon_text = "text-{$color}-600";
                    $count_text = "text-{$color}-700";
                    $hover_bg = "hover:bg-{$color}-50";
                    ?>
                    <a href="<?= htmlspecialchars($card['link']) ?>" class="block">
                        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 <?= $border ?> flex items-center space-x-4 transition duration-300 ease-in-out <?= $hover_bg ?>">
                            <div class="flex-shrink-0 p-3 rounded-full <?= $icon_bg ?> <?= $icon_text ?>">
                                <span class="material-icons text-3xl"><?= htmlspecialchars($card['icon']) ?></span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                                    <?= htmlspecialchars($card['label']) ?>
                                </div>
                                <div class="text-3xl font-extrabold <?= $count_text ?>">
                                    <?= htmlspecialchars($card['count']) ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </main>
    </div>
</body>

</html>