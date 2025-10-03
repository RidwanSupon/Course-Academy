<?php
require_once __DIR__ . '/config.php';

// Fetch data safely
function fetchData($pdo, $query) {
    try { return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC); } 
    catch(PDOException $e) { return []; }
}

// Fetching all necessary data
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
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<style>
    :root {
        /* Primary Dark Color (from your logo - deep navy/charcoal) */
        --ilm-blue: #1F2937; 
        /* Secondary Accent Color (Professional Blue/Cyan) */
        --ilm-accent: #007BFF;
    }
    .ilm-bg-blue { background-color: var(--ilm-blue); }
    .ilm-text-accent { color: var(--ilm-accent); }
    .ilm-bg-accent { background-color: var(--ilm-accent); }
    .ilm-text-blue { color: var(--ilm-blue); } 


:root { --ilm-blue:#0b1d3d; --ilm-gold:#f2a900; }
.ilm-bg-blue { background-color: var(--ilm-blue); }
.ilm-text-gold { color: var(--ilm-gold); }
.ilm-bg-gold { background-color: var(--ilm-gold); }
.bg-pattern { background-image: url('assets/images/bg-pattern.png'); background-size: cover; }
</style>
</head>
<body class="bg-gray-50 font-sans">

<?php include 'includes/header.php'; ?>

<main class="relative">

<!-- Banner Slider -->
<!-- Banner Slider -->
<section class="relative w-full overflow-hidden">
  <div class="relative w-full h-[300px] sm:h-[400px] md:h-[500px]">
    <?php foreach($banners as $i => $b): ?>
      <div class="banner-slide absolute inset-0 transition-all duration-700 ease-in-out <?= $i===0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>">
        <img src="assets/uploads/banners/<?= htmlspecialchars($b['image']) ?>" 
             alt="<?= htmlspecialchars($b['title'] ?? 'Banner') ?>" 
             class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center px-4">
          <?php if(!empty($b['title'])): ?>
            <h2 class="text-white text-xl sm:text-2xl md:text-4xl font-bold drop-shadow-lg">
              <?= htmlspecialchars($b['title']) ?>
            </h2>
          <?php endif; ?>
          <?php if(!empty($b['subtitle'])): ?>
            <p class="text-white text-sm sm:text-base md:text-lg mt-2 drop-shadow-lg max-w-lg">
              <?= htmlspecialchars($b['subtitle']) ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- About Us Section -->
<?php

// This line includes the entire content of the about_us.php file
include 'sections/about.php';

// ... rest of your index.php code, e.g., the footer include ...
?>


<!--courses-->
<section id="courses" class="py-20 px-6 bg-gray-50 text-center">
    <h2 class="text-4xl md:text-5xl font-black text-blue-900 mb-16 relative inline-block border-b-4 border-gray-200 pb-3" data-aos="fade-down">
        Our Featured Courses
        <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-24 h-1 ilm-bg-gold rounded-full shadow-md"></span>
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-10 max-w-7xl mx-auto">
        <?php 
        $delay = 0; // Initialize delay counter
        foreach($courses as $course): 
        ?>
            <a href="course.php?id=<?= $course['id'] ?>" 
                class="group relative flex flex-col p-6 sm:p-8 rounded-2xl border border-gray-200 
                       bg-blue-900 shadow-xl 
                       
                       /* --- HOVER ANIMATION CLASSES --- */
                       transition-all duration-300 ease-in-out 
                       transform hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-gray-300/80"
                
                data-aos="fade-up"
                data-aos-delay="<?= $delay ?>"
                data-aos-easing="ease-out-cubic"
            >
                
                <div class="w-16 h-16 flex items-center justify-center mx-auto mb-5 rounded-full 
                            
                            /* --- DEFAULT STATE: Clean Gray/White --- */
                            bg-gray-100 font-bold text-3xl border border-gray-300
                            
                            /* --- HOVER STATE: Flip to Dark Blue Background --- */
                            group-hover:bg-ilm-blue group-hover:border-ilm-blue
                            transition duration-300">
                    
                    <span class="transition duration-300">
                        <img src="assets/uploads/courseimg.png" alt="" class="w-8 h-8 group-hover:filter group-hover:invert transition duration-300">
                    </span>
                </div>

                <h3 class="text-xl font-extrabold text-white mb-3 transition duration-300 group-hover:ilm-text-accent">
                    <?= htmlspecialchars($course['title']) ?>
                </h3>

                <p class="text-gray-500 text-sm mb-4 flex-grow transition duration-300 group-hover:text-gray-600">
                    <?= htmlspecialchars($course['short_desc']) ?>
                </p>

             <div class="mt-4 pt-4 border-t border-gray-200 transition duration-300 group-hover:border-ilm-accent/50 flex justify-center">
    <span class="text-sm font-black text-blue-900 flex items-center">
        View Details 
        <span class="ml-1 group-hover:ml-2 transition-all duration-300">→</span>
    </span>
</div>
                
            </a>
        <?php 
        $delay += 100;
        endforeach; 
        ?>
    </div>
</section>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  // Initialize AOS after the page loads
  AOS.init({
    duration: 800, // duration of the animation (in milliseconds)
    once: true,    // elements should only animate once
  });



</script> 


<!-- Why Choose Us Section -->
<?php
    // Include the saved file here
    require 'sections/why_choose_us.php'; 
?>

<!-- Video & Gallery -->
<section id="gallery" class="ilm-bg-blue text-white py-16 px-6 text-center">
  <h2 class="text-4xl font-extrabold ilm-text-gold mb-4">Video Gallery</h2>
  <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
    <?php foreach(array_slice($videos,0,4) as $video):
      preg_match("/(?:youtube\.com\/.*v=|youtu\.be\/)([^&\n]+)/",$video['url'],$matches);
      $youtube_id = $matches[1] ?? null;
      if(!$youtube_id) continue;
    ?>
      <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-2xl bg-gray-900">
        <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?= htmlspecialchars($youtube_id) ?>" title="<?= htmlspecialchars($video['title']) ?>" frameborder="0" allowfullscreen></iframe>
      </div>
    <?php endforeach; ?>
  </div>
  <a href="videos.php" class="inline-block ilm-bg-gold text-ilm-blue py-2 px-6 rounded-lg font-bold hover:opacity-90 transition mb-10">See All Videos</a>

  <h2 class="text-4xl font-extrabold ilm-text-gold mb-4">Photo Gallery</h2>

  <!-- Slider After Photo Gallery Heading -->
  <div class="relative max-w-4xl mx-auto mb-6">
    <div id="photo-slider" class="overflow-hidden rounded-lg shadow-xl">
      <div class="flex transition-transform duration-500" id="slider-track">
        <?php foreach(array_slice($gallery,0,6) as $g): ?>
          <div class="min-w-full h-64">
            <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" 
                 alt="<?= htmlspecialchars($g['caption']) ?>" 
                 class="w-full h-full object-cover cursor-pointer">
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <button onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❮</button>
    <button onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❯</button>
  </div>

  <!-- Photo Grid -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto mb-6">
    <?php foreach(array_slice($gallery,0,6) as $g): ?>
      <div class="h-48 rounded-lg shadow-xl overflow-hidden">
        <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['caption']) ?>" class="w-full h-full object-cover cursor-pointer">
      </div>
    <?php endforeach; ?>
  </div>
  <a href="gallery.php" class="inline-block ilm-bg-gold text-ilm-blue py-2 px-6 rounded-lg font-bold hover:opacity-90 transition">See All Photos</a>
</section>

<script>
let currentSlide = 0;
const sliderTrack = document.getElementById('slider-track');
const slides = sliderTrack.children;
const totalSlides = slides.length;

function showSlide(index) {
    if(index < 0) index = totalSlides - 1;
    if(index >= totalSlides) index = 0;
    currentSlide = index;
    sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
}

function nextSlide() { showSlide(currentSlide + 1); }
function prevSlide() { showSlide(currentSlide - 1); }

// Auto-slide every 5 seconds
setInterval(() => { nextSlide(); }, 5000);
</script>

<section id="reviews" class="bg-white py-16 px-6 mx-auto">
    <h2 class="text-4xl font-extrabold ilm-text-gold mb-10 text-center">Student Reviews</h2>

    <div class="relative max-w-6xl mx-auto overflow-hidden">
        <div id="review-slider" class="flex transition-transform duration-500 ease-in-out">
            <?php foreach($reviews as $rev): ?>
                <div class="review-item flex-none w-full md:w-1/2 px-3">
                    <div class="bg-gray-100 p-6 rounded-lg shadow-xl border-l-4 border-ilm-text-gold">
                        <div class="flex items-start mb-4">
                            <img 
                                src="<?= $rev['image'] ? 'assets/uploads/reviews/'.htmlspecialchars($rev['image']) : 'assets/images/student-placeholder.jpg' ?>" 
                                alt="Student" 
                                class="w-16 h-16 rounded-full mr-4 object-cover border-2 border-ilm-blue"
                            >
                            <div class="text-left">
                                <p class="font-semibold text-lg"><?= htmlspecialchars($rev['name']) ?></p>
                                <p class="text-gray-500 text-sm"><?= htmlspecialchars($rev['country']) ?></p>
                                <div class="flex text-yellow-500"><?= str_repeat('⭐', intval($rev['rating'])) ?></div>
                            </div>
                        </div>
                        <p class="text-gray-700 italic text-sm">"<?= htmlspecialchars($rev['message']) ?>"</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Buttons -->
        <button onclick="prevReview()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❮</button>
        <button onclick="nextReview()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❯</button>
    </div>
</section>

<script>
const slider = document.getElementById('review-slider');
const items = document.querySelectorAll('.review-item');
let index = 0;

// Determine how many items per slide based on screen width
function itemsPerSlide() {
    return window.innerWidth >= 768 ? 2 : 1; // 2 on desktop, 1 on mobile
}

function updateSlider() {
    const perSlide = itemsPerSlide();
    const offset = index * (100 / perSlide);
    slider.style.transform = `translateX(-${offset}%)`;
}

function nextReview() {
    const perSlide = itemsPerSlide();
    index = (index + 1) % Math.ceil(items.length / perSlide);
    updateSlider();
}

function prevReview() {
    const perSlide = itemsPerSlide();
    index = (index - 1 + Math.ceil(items.length / perSlide)) % Math.ceil(items.length / perSlide);
    updateSlider();
}

// Auto slide every 5 seconds
setInterval(nextReview, 5000);

// Update slider on resize
window.addEventListener('resize', updateSlider);

// Initial update
updateSlider();
</script>

</main>
<?php include 'includes/footer.php'; ?>


<!-- Banner Slider Script -->

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.banner-slide');
    let index = 0;

    if(slides.length > 1){
        setInterval(() => {
            // Hide current slide
            slides[index].classList.remove('opacity-100', 'z-10');
            slides[index].classList.add('opacity-0', 'z-0');

            // Move to next
            index = (index + 1) % slides.length;

            // Show next slide
            slides[index].classList.remove('opacity-0', 'z-0');
            slides[index].classList.add('opacity-100', 'z-10');
        }, 5000);
    }
});
</script>

</body>
</html>
