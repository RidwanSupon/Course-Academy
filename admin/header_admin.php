<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Optional: highlight current page
$current = basename($_SERVER['PHP_SELF']);
?>
<header class="bg-indigo-700 text-white shadow">
  <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
    <!-- Logo / Title -->
    <a href="index.php" class="text-xl font-bold tracking-wide">
      Admin Dashboard
    </a>

    <!-- Navigation -->
<nav class="space-x-4">
  <a href="index.php" class="<?= $current === 'index.php' ? 'font-bold underline' : '' ?> hover:underline">Dashboard</a>
  <a href="banners.php" class="<?= $current === 'banners.php' ? 'font-bold underline' : '' ?> hover:underline">Banners</a>
  <a href="videos.php" class="<?= $current === 'videos.php' ? 'font-bold underline' : '' ?> hover:underline">Videos</a>
  <a href="gallery.php" class="<?= $current === 'gallery.php' ? 'font-bold underline' : '' ?> hover:underline">Gallery</a>
  <a href="reviews.php" class="<?= $current === 'student_reviews.php' ? 'font-bold underline' : '' ?> hover:underline">Reviews</a>
  <a href="enrollments.php" class="<?= $current === 'enrollments.php' ? 'font-bold underline' : '' ?> hover:underline">Enrollments</a>
  <a href="mentors.php" class="<?= $current === 'mentors.php' ? 'font-bold underline' : '' ?> hover:underline">Mentors</a>
</nav>


    <!-- Logout -->
    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded">
      Logout
    </a>
  </div>
</header>
