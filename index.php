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

<!--about us-->

<section id="about-us" class="bg-gray-50 py-16 px-4 sm:px-6 lg:px-8 text-blue-900">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-extrabold text-gray-900 mb-12 text-center">About At-Tatweer Institute</h2>

        <div class="flex flex-col lg:flex-row lg:space-x-12 space-y-12 lg:space-y-0">

            <!-- Left Column -->
            <div 
                class="lg:w-1/2 flex flex-col justify-center opacity-0 lg:animate-slide-in-left"
                data-aos="fade-right" 
                data-aos-duration="1000" 
                data-aos-once="true"
            >
                <h3 class="text-2xl font-bold ilm-text-gold mb-4">Our Mission</h3>
                <p class="text-gray-900 mb-6">
                    At-Tatweer Institute is a trusted and pioneering online platform dedicated to academic Islamic education with a modern vision. Our mission is to empower students with authentic Islamic knowledge while equipping them with the intellectual and professional skills necessary to excel in today’s world.
                </p>
                <p class="text-gray-900">
                    Under the direct supervision of distinguished scholars from Al-Azhar University, we provide a well-structured learning environment where traditional Islamic sciences are taught alongside contemporary subjects. This unique approach ensures that students develop a strong foundation in faith, character, and knowledge — including recitation and memorization of the Holy Qur’an, Aqeedah, Hadith, Tafsir, Fiqh, Arabic literature, and spoken English — while also preparing them to meet the challenges of modern society.
                </p>
            </div>

            <!-- Right Column -->
            <div 
                class="lg:w-1/2 flex flex-col justify-center opacity-0 lg:animate-slide-in-right"
                data-aos="fade-left" 
                data-aos-duration="1000" 
                data-aos-once="true"
            >
                <h3 class="text-2xl font-bold  ilm-text-gold mb-4">Our Commitment</h3>
                <ul class="list-disc pl-5 text-gray-900 mb-6 space-y-2">
                    <li>Authentic Islamic scholarship guided by world-renowned scholars</li>
                    <li>Modern academic and professional courses tailored to today’s needs</li>
                    <li>Well-structured online classes and resources accessible from anywhere in the world</li>
                    <li>A student-centered approach with personalized guidance and mentorship</li>
                    <li>A safe, reliable, and inspiring environment for learners of all ages</li>
                </ul>

                <h3 class="text-2xl font-bold ilm-text-gold mb-4">What Makes Us Different</h3>
                <ul class="list-disc pl-5 text-gray-900 space-y-2">
                    <li>A recognized and reliable source of authentic Islamic education</li>
                    <li>A unique balance between Islamic tradition and modern learning</li>
                    <li>Direct access to qualified, reputable, and internationally respected instructors</li>
                    <li>Opportunities for both children and adults to learn with excellence from the comfort of their homes</li>
                </ul>
            </div>
        </div>

        <p class="mt-12 text-center text-gray-700 text-lg opacity-0 lg:animate-slide-in-left" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
            At-Tatweer Institute is more than just an educational platform — it is a gateway to building a future rooted in knowledge, values, and excellence. <br>
            <strong>Join us today and take the first step toward a transformative learning experience that nurtures both faith and intellect.</strong>
        </p>
    </div>
</section>

<script>
    // Initialize AOS
    AOS.init({
        once: true, // animation happens only once when scrolling
        disable: function() {
            // Disable AOS for desktop (min-width: 1024px)
            return window.innerWidth >= 1024;
        }
    });
</script>



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



</script>  <!-- why choose us --> 
</section><section id="why-choose-us" class="bg-gray-50 py-16 px-6">
<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

  <!-- Desktop View (unchanged) -->
  <div class="relative min-h-[400px] hidden md:block">
      <div class="absolute w-64 h-80 bg-white p-2 rounded-lg shadow-xl border border-gray-100 transform rotate-3 z-20"
           style="top: 50%; left: 20%; transform: translate(-50%, -50%) rotate(3deg);">
          <img src="assets/uploads/faq-1.png" alt="Teacher on laptop" class="w-full h-full object-cover rounded">
          <span class="absolute top-2 left-2 w-3 h-3 bg-green-600 rounded-full"></span>
      </div>

      <div class="absolute w-48 h-48 bg-white p-2 rounded-lg shadow-xl border border-gray-100 transform -rotate-6 z-10"
           style="top: 10%; left: 65%; transform: translateX(-50%) rotate(-6deg);">
          <img src="assets/uploads/faq-2.png" alt="Children learning" class="w-full h-full object-cover rounded">
          <span class="absolute top-2 right-2 w-3 h-3 bg-red-600 rounded-full"></span>
      </div>

      <div class="absolute w-56 h-40 bg-white p-2 rounded-lg shadow-xl border border-gray-100 transform -rotate-3 z-10"
           style="bottom: 10%; right: 5%; transform: rotate(-3deg);">
          <img src="assets/uploads/faq-3.png" alt="Children on cushions" class="w-full h-full object-cover rounded">
          <span class="absolute bottom-2 right-2 w-3 h-3 bg-red-600 rounded-full"></span>
      </div>
  </div>

  <!-- Mobile View -->
  <div class="relative min-h-[400px] flex justify-center items-center md:hidden space-x-4">
      <!-- First Image -->
      <div class="w-52 h-64 bg-white p-2 rounded-lg shadow-xl border border-gray-100 animate-float">
          <img src="assets/uploads/faq-1.png" alt="Teacher on laptop" class="w-full h-full object-cover rounded">
          <span class="absolute top-2 left-2 w-3 h-3 bg-green-600 rounded-full"></span>
      </div>
      <!-- Second Image -->
      <div class="w-44 h-44 bg-white p-2 rounded-lg shadow-xl border border-gray-100 animate-float-delay">
          <img src="assets/uploads/faq-2.png" alt="Children learning" class="w-full h-full object-cover rounded">
          <span class="absolute top-2 right-2 w-3 h-3 bg-red-600 rounded-full"></span>
      </div>
      <!-- Third Image -->
      <div class="w-48 h-36 bg-white p-2 rounded-lg shadow-xl border border-gray-100 animate-float-delay2">
          <img src="assets/uploads/faq-3.png" alt="Children on cushions" class="w-full h-full object-cover rounded">
          <span class="absolute bottom-2 right-2 w-3 h-3 bg-red-600 rounded-full"></span>
      </div>
  </div>

<!-- Mobile Animation -->
<style>
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
.animate-float-delay {
  animation: float 3s ease-in-out 0.5s infinite;
}
.animate-float-delay2 {
  animation: float 3s ease-in-out 1s infinite;
}
</style>

        <!-- FAQ Accordion -->
        <div class="flex flex-col justify-center h-full"> <h2 class="text-4xl font-extrabold text-blue-900 mb-6">Why Choose At-Tatweer International Institute?</h2>
            
        <div class="space-y-3" id="faq-accordion">
                
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
                        Advanced Online Islamic Education
                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
learning that blends authentic Islamic knowledge with modern academic insight. Through our state-of-the-art e-learning platform, students of all ages and backgrounds can join from anywhere in the world and benefit from uninterrupted, specialized education.                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
A Global Learning Environment
                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
At-Tatweer International Institute brings together students from different cultures and societies. This global classroom broadens perspectives, nurtures critical thinking, and strengthens the ability to engage in meaningful dialogue—skills essential for success in today’s interconnected world.                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
Nurturing a Qur’anic Generation                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
Guided by expert scholars of Qur’anic sciences, our programs ensure precise memorization, authentic recitation, and mastery of the well-established mutawātir qirāʾāt with an unbroken chain of transmission (sanad). Through our structured Ten-Level syllabus, students gain both Qur’anic fluency and essential Islamic knowledge. <br>Special Programs for Adults
We provide dedicated Qur’an courses tailored for adults. Designed to be simple, clear, and engaging, these programs help learners build a strong foundation in Qur’an, Sunnah, and essential Islamic principles at any stage of life.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
Global Reach                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
Students from across the globe enroll in our programs, connecting hearts and minds through knowledge rooted in Qur’an and Sunnah. <br>
Our classes are available in Arabic, English, Bangla, and Urdu, making learning accessible and effective for diMultilingual Education
verse learners.                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
                      Flexible & Interactive Learning
                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
We offer lively, student-centered classes that are interactive, flexible, and designed to fit each learner’s schedule.                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
Free Trial Classes                        <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
Experience our teaching quality firsthand with a free trial class before enrolling.
                    </div>
                </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Excellence with Authenticity
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            We combine the timeless guidance of Qur’an and Sunnah with modern educational methods to ensure learning that is both authentic and impactful.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Holistic Growth
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            Education at At-Tatbeer focuses not only on knowledge, but also on building character, discipline, and moral values.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Recognized & Evolving Curriculum
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            For secondary and higher-secondary levels, we follow the curriculum of Ma’had al-Buʿūth al-Islāmiyyah (an Azhar-affiliated institute), alongside traditional madrasah syllabi. Our academic board continuously updates and refines study materials to ensure authenticity and relevance. Core subjects include Tafsīr, Hadīth, Fiqh, ʿAqīdah, and Tarbiyah.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Excellence in Languages (English & Arabic)
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            Through Nadim’s Academy, we offer a comprehensive English program from Basic to Advanced, alongside classical and modern Arabic studies. We use internationally recognized teaching methodologies designed specifically for non-Arab learners to build linguistic fluency and confidence.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            World-Class Faculty
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            Our instructors include graduates from Al-Azhar University, Darul Uloom Deoband, and other globally respected institutions. Their expertise ensures students achieve mastery in Islamic sciences and intellectual maturity.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Academic Oversight & Feedback
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            A professional panel monitors and evaluates all programs, ensuring quality and consistent progress. We maintain an open channel for students and parents to share feedback freely, allowing us to take necessary steps promptly.
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
        <button class="accordion-header flex justify-between items-center w-full p-4 text-left text-lg font-semibold text-gray-800 hover:bg-gray-50 transition duration-150" aria-expanded="false">
            Our Mission
            <svg class="accordion-icon w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content p-4 pt-0 text-gray-600 hidden">
            At-Tatweer International Institute is more than a school—it is a global platform where knowledge meets transformation. Our mission is to facilitate a global, accessible, and high-quality platform for Quranic and Arabic education, empowering every Muslim to connect deeply with the book of Allah, grow into individuals whose lives are guided by Qur’an and Sunnah.
        </div>
    </div>

            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const accordionHeaders = document.querySelectorAll('.accordion-header');

        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const icon = header.querySelector('.accordion-icon');
                
                // Toggle the 'hidden' class on the content
                content.classList.toggle('hidden');
                
                // Toggle the 'aria-expanded' attribute
                const isExpanded = header.getAttribute('aria-expanded') === 'true' || false;
                header.setAttribute('aria-expanded', !isExpanded);
                
                // Rotate the icon
                icon.classList.toggle('rotate-180');
            });
        });
    });
</script>



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
