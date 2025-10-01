<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// get counts for dashboard
$counts = [];
$counts['banners'] = $pdo->query("SELECT COUNT(*) FROM banners")->fetchColumn();
$counts['courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$counts['videos'] = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$counts['gallery'] = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
$counts['enrollments'] = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE status='Pending'")->fetchColumn();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="flex">
    <aside class="w-64 bg-white p-6 border-r">
      <h3 class="font-bold mb-4">Admin</h3>
      <nav class="flex flex-col space-y-2">
        <a href="dashboard.php" class="px-3 py-2 rounded bg-indigo-50">Dashboard</a>
        <a href="banners.php" class="px-3 py-2 rounded">Banners</a>
        <a href="courses.php" class="px-3 py-2 rounded">Courses</a>
        <a href="videos.php" class="px-3 py-2 rounded">Videos</a>
        <a href="gallery.php" class="px-3 py-2 rounded">Gallery</a>
        <a href="enrollments.php" class="px-3 py-2 rounded">Enrollments (<?= $counts['enrollments'] ?>)</a>
        <a href="reviews.php" class="px-3 py-2 rounded">Reviews</a>
        <a href="logout.php" class="px-3 py-2 text-red-600">Logout</a>
      </nav>
    </aside>
    <main class="flex-1 p-8">
      <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Banners</div>
          <div class="text-2xl font-bold"><?= $counts['banners'] ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Courses</div>
          <div class="text-2xl font-bold"><?= $counts['courses'] ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Videos</div>
          <div class="text-2xl font-bold"><?= $counts['videos'] ?></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Gallery</div>
          <div class="text-2xl font-bold"><?= $counts['gallery'] ?></div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
