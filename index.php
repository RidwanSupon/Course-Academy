<?php
require_once __DIR__ . '/config.php';

// Fetch data
function fetchData($pdo, $query) {
    try { return $pdo->query($query)->fetchAll(); } 
    catch(PDOException $e) { return []; }
}

$banners = fetchData($pdo, "SELECT * FROM banners WHERE active=1 ORDER BY sort_order DESC, id DESC");
$courses = fetchData($pdo, "SELECT * FROM courses WHERE active=1 ORDER BY created_at DESC");
$gallery = fetchData($pdo, "SELECT * FROM gallery ORDER BY created_at DESC");
$reviews = fetchData($pdo, "SELECT * FROM reviews ORDER BY created_at DESC");
$videos = fetchData($pdo, "SELECT * FROM videos ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ILM PATH NETWORK - Your Path to Quranic Mastery</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root { --ilm-blue:#0b1d3d; --ilm-gold:#f2a900; }
.ilm-bg-blue { background-color: var(--ilm-blue); }
.ilm-text-gold { color: var(--ilm-gold); }
.ilm-bg-gold { background-color: var(--ilm-gold); }
.bg-pattern { background-image: url('assets/images/bg-pattern.png'); background-size: cover; }
</style>
</head>
<body class="bg-gray-50 font-sans">

<?php include 'includes/header.php'; ?>

<main class="pt-20">

<!-- Banner Slider -->
<section class="relative w-full overflow-hidden">
  <div class="relative w-full h-[300px] sm:h-[400px] md:h-[500px]">
    <?php foreach($banners as $i => $b): ?>
      <div class="banner-slide absolute inset-0 transition-opacity duration-700 <?= $i===0 ? 'opacity-100 z-10' : 'opacity-0' ?>">
        <img src="assets/uploads/banners/<?= htmlspecialchars($b['image']) ?>" alt="<?= htmlspecialchars($b['title'] ?? 'Banner') ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center px-4">
          <?php if(!empty($b['title'])): ?>
            <h2 class="text-white text-xl sm:text-2xl md:text-4xl font-bold drop-shadow-lg"><?= htmlspecialchars($b['title']) ?></h2>
          <?php endif; ?>
          <?php if(!empty($b['subtitle'])): ?>
            <p class="text-white text-sm sm:text-base md:text-lg mt-2 drop-shadow-lg max-w-lg"><?= htmlspecialchars($b['subtitle']) ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Why Choose Us -->
<section id="home" class="bg-pattern py-16 px-6 text-center">
    <h1 class="text-4xl font-extrabold ilm-text-gold mb-8">Why Choose Us?</h1>
    <div class="grid grid-cols-3 gap-3 max-w-sm mx-auto mb-10">
        <div class="col-span-2 row-span-2 h-48 bg-gray-300 rounded-lg shadow-xl overflow-hidden">
            <img src="assets/images/why_choose_1.jpg" alt="Teacher on laptop" class="w-full h-full object-cover">
        </div>
        <div class="col-span-1 h-24 bg-gray-300 rounded-lg shadow-xl overflow-hidden">
            <img src="assets/images/why_choose_2.jpg" alt="Children learning online" class="w-full h-full object-cover">
        </div>
        <div class="col-span-1 h-24 bg-gray-300 rounded-lg shadow-xl overflow-hidden">
            <img src="assets/images/why_choose_3.jpg" alt="Children on cushions" class="w-full h-full object-cover">
        </div>
    </div>
    <p class="text-gray-700 leading-relaxed text-left max-w-3xl mx-auto">
        ILM Path Network — Your Path to Quranic Mastery. Live, interactive classes guided by expert teachers for all ages. Courses include Quran memorization, Arabic language, and more.
    </p>
</section>

<!-- Courses Section -->
<section id="courses" class="py-16 px-6 text-center bg-white">
  <h2 class="text-4xl ilm-text-gold font-extrabold mb-8">Our Courses</h2>
  <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
    <?php foreach($courses as $course): ?>
      <div class="course-card p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-2xl transition duration-300 cursor-pointer"
           data-id="<?= $course['id'] ?>"
           data-title="<?= htmlspecialchars($course['title'], ENT_QUOTES) ?>"
           data-desc="<?= htmlspecialchars($course['short_desc'], ENT_QUOTES) ?>">
        <div class="text-5xl mb-3 ilm-text-gold">📖</div>
        <h3 class="text-2xl font-bold ilm-text-gold mb-3"><?= htmlspecialchars($course['title']) ?></h3>
        <p class="text-gray-600"><?= htmlspecialchars($course['short_desc']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Video & Gallery -->
<section id="gallery" class="ilm-bg-blue text-white py-16 px-6 text-center">
  <h2 class="text-4xl font-extrabold ilm-text-gold mb-4">Video Gallery</h2>
  <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6 mb-6">
    <?php foreach(array_slice($videos,0,4) as $video):
      preg_match("/(?:youtube\.com\/.*v=|youtu\.be\/)([^&\n]+)/",$video['url'],$matches);
      $youtube_id = $matches[1] ?? null;
    ?>
      <?php if($youtube_id): ?>
        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-2xl bg-gray-900">
          <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?= htmlspecialchars($youtube_id) ?>" title="<?= htmlspecialchars($video['title']) ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <a href="videos.php" class="inline-block ilm-bg-gold text-ilm-blue py-2 px-6 rounded-lg font-bold hover:opacity-90 transition mb-10">See All Videos</a>

  <h2 class="text-4xl font-extrabold ilm-text-gold mb-4">Photo Gallery</h2>
  <div class="grid grid-cols-2 gap-4 max-w-3xl mx-auto mb-6">
    <?php foreach(array_slice($gallery,0,6) as $g): ?>
      <div class="h-48 bg-gray-700 rounded-lg shadow-xl overflow-hidden">
        <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['caption']) ?>" class="w-full h-full object-cover cursor-pointer">
      </div>
    <?php endforeach; ?>
  </div>
  <a href="gallery.php" class="inline-block ilm-bg-gold text-ilm-blue py-2 px-6 rounded-lg font-bold hover:opacity-90 transition">See All Photos</a>
</section>

<!-- Student Reviews -->
<section id="reviews" class="bg-white py-16 px-6 text-center">
  <h2 class="text-4xl font-extrabold ilm-text-gold mb-10">Student Reviews</h2>
  <?php foreach($reviews as $rev): ?>
    <div class="max-w-xl mx-auto bg-gray-100 p-8 rounded-lg shadow-xl border-l-4 border-ilm-text-gold mb-6">
      <div class="flex items-start mb-4">
        <img src="<?= $rev['image']? 'assets/uploads/gallery/'.htmlspecialchars($rev['image']): 'assets/images/student-placeholder.jpg' ?>" alt="Student" class="w-16 h-16 rounded-full mr-4 object-cover border-2 border-ilm-blue">
        <div class="text-left">
          <p class="font-semibold text-xl"><?= htmlspecialchars($rev['name']) ?></p>
          <p class="text-gray-500 text-sm"><?= htmlspecialchars($rev['country']) ?></p>
          <div class="flex text-yellow-500"><?= str_repeat('⭐', intval($rev['rating'])) ?></div>
        </div>
      </div>
      <p class="text-gray-700 italic">"<?= htmlspecialchars($rev['message']) ?>"</p>
    </div>
  <?php endforeach; ?>
</section>

</main>

<!-- Enrollment Modal -->
<div id="enroll-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
  <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 relative">
    <button id="close-modal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
    <h2 id="modal-title" class="text-2xl font-bold ilm-text-gold mb-4">Enroll in Course</h2>
    <p id="modal-desc" class="text-gray-700 mb-4"></p>
    <form id="enroll-form" action="enroll.php" method="POST" class="space-y-4">
      <input type="hidden" name="course_id" id="course_id">
      <input type="text" name="name" placeholder="Full Name" required class="w-full border rounded-lg p-2">
      <input type="email" name="email" placeholder="Email" class="w-full border rounded-lg p-2">
      <input type="text" name="phone" placeholder="Phone" required class="w-full border rounded-lg p-2">
      <input type="text" name="location" placeholder="Location" class="w-full border rounded-lg p-2">
      <select name="payment_method" required class="w-full border rounded-lg p-2">
        <option value="Cash">Cash</option>
        <option value="bKash">bKash</option>
      </select>
      <button type="submit" class="ilm-bg-gold text-ilm-blue w-full py-2 rounded-lg font-bold hover:opacity-90 transition">Submit Enrollment</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Mobile menu toggle
const menuToggle = document.getElementById('menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');
menuToggle.addEventListener('click', () => mobileMenu.classList.toggle('translate-x-full'));
document.querySelectorAll('#mobile-menu a').forEach(link => link.addEventListener('click', () => mobileMenu.classList.add('translate-x-full')));

// Banner slider
let slides = document.querySelectorAll('.banner-slide');
let index = 0;
setInterval(() => {
    slides[index].classList.remove('opacity-100'); slides[index].classList.add('opacity-0');
    index = (index + 1) % slides.length;
    slides[index].classList.remove('opacity-0'); slides[index].classList.add('opacity-100');
}, 5000);

// Enrollment Modal
const enrollModal = document.getElementById('enroll-modal');
const closeModalBtn = document.getElementById('close-modal');
const courseInput = document.getElementById('course_id');
const modalTitle = document.getElementById('modal-title');
const modalDesc = document.getElementById('modal-desc');

// Open modal using data attributes
document.querySelectorAll('.course-card').forEach(card => {
    card.addEventListener('click', () => {
        courseInput.value = card.dataset.id;
        modalTitle.textContent = "Enroll in " + card.dataset.title;
        modalDesc.textContent = card.dataset.desc || "";
        enrollModal.classList.remove('hidden');
        enrollModal.classList.add('flex');
    });
});

closeModalBtn.addEventListener('click', () => {
    enrollModal.classList.add('hidden'); enrollModal.classList.remove('flex');
});

window.addEventListener('click', (e) => {
    if(e.target === enrollModal) { enrollModal.classList.add('hidden'); enrollModal.classList.remove('flex'); }
});
</script>
</body>
</html>
