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

// 🔴 IMPORTANT: Contact Details (You Must Update These!) 🔴
$whatsapp_number = "8801677689098"; // আপনার হোয়াটসঅ্যাপ নম্বর দিন (যেমন: 8801712345678)
$email_address = "ridwansupon@gmail.com"; // আপনার ইমেল অ্যাড্রেস দিন
// -----------------------------------------------------------

// Prepare WhatsApp link (remove non-numeric characters for clean link)
$whatsapp_link = "https://wa.me/" . htmlspecialchars(preg_replace('/[^0-9]/', '', $whatsapp_number));
$email_link = "mailto:" . htmlspecialchars($email_address);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ILM PATH NETWORK - Your Path to Quranic Mastery</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

<style>
    /* Global CSS Variables */
    :root {
        --ilm-blue: #0b1d3d; /* Deep Navy */
        --ilm-gold: #f2a900; /* Gold Accent */
    }
    .ilm-bg-blue { background-color: var(--ilm-blue); }
    .ilm-text-gold { color: var(--ilm-gold); }
    .ilm-bg-gold { background-color: var(--ilm-gold); }
    .bg-pattern { background-image: url('assets/images/bg-pattern.png'); background-size: cover; }

    /* --- Floating Social Icons CSS (Default/Desktop) --- */
    .floating-social-icons {
        position: fixed; /* Key: Makes it fixed when scrolling */
        top: 50%; 
        right: 0; /* Aligns to the right edge */
        transform: translateY(-50%);
        z-index: 1000;
        opacity: 0.8; /* Default 80% opacity */
    }
    .floating-social-icons a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px; 
        height: 50px;
        margin-bottom: 8px; 
        border-radius: 8px 0 0 8px; /* Rounded on the left, flat on the right */
        transition: opacity 0.3s, transform 0.3s;
        box-shadow: -2px 4px 10px rgba(0, 0, 0, 0.3);
    }
    .floating-social-icons a:hover {
        opacity: 1;
        transform: translateX(-5px) translateY(-50%); /* Slide left slightly on hover */
    }

    /* 📌 Mobile View Customization (max-width: 767px is standard for Tailwind 'md' breakpoint) */
    @media (max-width: 767px) {
        .floating-social-icons {
            top: 70%; /* ⬇️ Key: 50% থেকে 70% এ নামানো হয়েছে */
            opacity: 0.5; /* ⬇️ Key: 50% opacity as requested for mobile */
        }
        .floating-social-icons a:hover {
             /* মোবাইল হোভার ইফেক্ট, যাতে ফিক্সড পজিশন ঠিক থাকে */
             transform: translateX(-5px) translateY(0); 
        }
    }
    /* --- END Floating Social Icons CSS --- */

</style>
</head>
<body class="bg-gray-50 font-sans">

<div class="floating-social-icons">
    <a href="<?= $whatsapp_link ?>" 
        target="_blank" 
        class="bg-green-500 text-white">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>

    <a href="<?= $email_link ?>" 
        class="bg-red-500 text-white">
        <i class="fas fa-envelope text-2xl"></i>
    </a>
</div>
<?php include 'includes/header.php'; ?>

<main class="relative">

<section class="relative w-full overflow-hidden">
    <div class="relative w-full h-[500px] sm:h-[400px] md:h-[500px] lg:h-[500px] bg-gray-100 shadow-xl">
        
        <?php foreach($banners as $i => $b): ?>
            <div class="banner-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?= $i===0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>">
                
                <img src="assets/uploads/banners/<?= htmlspecialchars($b['image']) ?>" 
                    alt="<?= htmlspecialchars($b['title'] ?? 'Academy Banner') ?>" 
                    class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-start text-left px-6 sm:px-12 md:px-20 lg:px-32">
                    <div class="max-w-xl">
                        <?php if(!empty($b['title'])): ?>
                            <h2 class="text-white text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-2 drop-shadow-lg leading-snug">
                                <?= htmlspecialchars($b['title']) ?>
                            </h2>
                        <?php endif; ?>
                        
                        <?php if(!empty($b['subtitle'])): ?>
                            <p class="text-gray-100 text-base sm:text-lg md:text-xl mt-2 mb-4 font-medium drop-shadow-sm">
                                <?= htmlspecialchars($b['subtitle']) ?>
                            </p>
                        <?php endif; ?>
                        
                        </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
            <?php foreach($banners as $i => $b): ?>
                <button class="slide-indicator w-3 h-3 rounded-full transition duration-300 <?= $i===0 ? 'bg-green-500' : 'bg-white/50 hover:bg-green-500/70' ?>" data-slide-index="<?= $i ?>"></button>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<script>
    // Get all slides and indicator buttons
    const slides = document.querySelectorAll('.banner-slide');
    const indicators = document.querySelectorAll('.slide-indicator');
    const totalSlides = slides.length;
    let currentSlide = 0; // The index of the currently visible slide

    if (totalSlides > 0) {
        // Function to show a specific slide
        function showSlide(index) {
            // Remove active classes from all slides and indicators
            slides.forEach(slide => {
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'z-0');
            });
            indicators.forEach(indicator => {
                // Tailwind classes for active/inactive state
                indicator.classList.remove('bg-white', 'bg-green-500'); // Assuming green-500 is your active color
                indicator.classList.add('bg-white/50', 'hover:bg-white'); 
            });

            // Add active classes to the target slide and indicator
            slides[index].classList.remove('opacity-0', 'z-0');
            slides[index].classList.add('opacity-100', 'z-10');
            
            indicators[index].classList.remove('bg-white/50', 'hover:bg-white');
            // NOTE: Use 'bg-white' or the color you used in your CSS for the active indicator
            indicators[index].classList.add('bg-white'); 

            currentSlide = index;
        }

        // --- 1. Dot Button Click Functionality ---
        indicators.forEach(indicator => {
            indicator.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.slideIndex);
                showSlide(index);
                // Reset the auto-slide timer when manually changing slide
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000); // 5 seconds interval
            });
        });

        // --- 2. Auto Slide Functionality ---
        function nextSlide() {
            let nextIndex = (currentSlide + 1) % totalSlides;
            showSlide(nextIndex);
        }

        // Start the automatic slide transition (e.g., every 5 seconds)
        let slideInterval = setInterval(nextSlide, 5000); // 5000 milliseconds = 5 seconds
    }
</script>



<?php
// This line includes the entire content of the about_us.php file
include 'sections/about.php';
?>

---

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
    <span class="text-sm font-black text-white flex items-center">
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

---

<?php
    // Include the saved file here
    require 'sections/why_choose_us.php'; 
?>

---

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
    </div><a href="videos.php" 
    class="
        /* Layout & Positioning */
        inline-block 
        
        /* Typography */
        text-white 
        text-lg 
        font-extrabold 
        uppercase 
        tracking-wider 

        /* Spacing & Shape */
        py-3 
        px-10 
        rounded-full /* Pill shape */
        mb-10 

        /* The Fancy Part: Gradient & Shadow */
        bg-gradient-to-r 
        from-amber-400 
        to-orange-600 
        shadow-lg 
        shadow-amber-500/50 
        
        /* Hover & Transition Effects */
        transition-all 
        duration-300 
        ease-in-out 
        hover:scale-105 
        hover:shadow-xl 
        hover:shadow-orange-400/70
        hover:ring-4
        hover:ring-amber-300/50

        /* Active State (for a slight press effect) */
        active:scale-100
        active:shadow-lg
    "
>
    See All Videos
</a>
    <h2 class="text-4xl font-extrabold ilm-text-gold mb-4">Photo Gallery</h2>

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
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto mb-6">
        <?php foreach(array_slice($gallery,0,6) as $g): ?>
            <div class="h-48 rounded-lg shadow-xl overflow-hidden">
                <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['caption']) ?>" class="w-full h-full object-cover cursor-pointer">
            </div>
        <?php endforeach; ?>
    </div>
<a href="gallery.php" 
    class="
        /* Layout & Positioning */
        inline-block 
        
        /* Typography */
        text-white 
        text-lg 
        font-extrabold 
        uppercase 
        tracking-wider 
        
        /* Spacing & Shape */
        py-3 
        px-10 
        rounded-full /* Pill shape */
        
        /* The Fancy Part: Gradient & Shadow */
        bg-gradient-to-r 
        from-amber-400 
        to-orange-600 
        shadow-lg 
        shadow-amber-500/50 
        
        /* Hover & Transition Effects */
        transition-all 
        duration-300 
        ease-in-out 
        hover:scale-105 
        hover:shadow-xl 
        hover:shadow-orange-400/70
        hover:ring-4
        hover:ring-amber-300/50

        /* Active State (for a slight press effect) */
        active:scale-100
        active:shadow-lg
    "
>
    See All Photos
</a>
</section>

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

    </div>
</section>

</main>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
// Initialize AOS after the page loads
AOS.init({
    duration: 800, // duration of the animation (in milliseconds)
    once: true,    // elements should only animate once
});
</script> 

<script>
// Photo Gallery Slider Script
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
if(totalSlides > 1) { // Only auto-slide if there's more than one slide
    setInterval(() => { nextSlide(); }, 5000);
}
</script>

<script>
// Review Slider Script
const reviewSlider = document.getElementById('review-slider');
const reviewItems = document.querySelectorAll('.review-item');
let reviewIndex = 0;

// Determine how many items per slide based on screen width
function itemsPerSlide() {
    return window.innerWidth >= 768 ? 2 : 1; // 2 on desktop, 1 on mobile
}

function updateReviewSlider() {
    const perSlide = itemsPerSlide();
    const maxIndex = Math.ceil(reviewItems.length / perSlide);
    
    // Clamp the index to prevent sliding past the end
    if (reviewIndex >= maxIndex) reviewIndex = 0; 
    if (reviewIndex < 0) reviewIndex = maxIndex - 1;

    const offset = reviewIndex * (100 / perSlide);
    reviewSlider.style.transform = `translateX(-${offset}%)`;
}

function nextReview() {
    const perSlide = itemsPerSlide();
    reviewIndex = (reviewIndex + 1) % Math.ceil(reviewItems.length / perSlide);
    updateReviewSlider();
}

// Auto slide every 5 seconds (only if enough items for sliding)
if (reviewItems.length > itemsPerSlide()) {
    setInterval(nextReview, 5000);
}

// Update slider on resize and initial load
window.addEventListener('resize', updateReviewSlider);
updateReviewSlider();
</script>

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

<?php include 'includes/footer.php'; ?>
</body>
</html>