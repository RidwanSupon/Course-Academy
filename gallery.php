<?php
require_once 'config.php';
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<main class="pt-20 py-16 px-6 text-center bg-gray-50">
    <h2 class="text-4xl ilm-text-gold font-extrabold mb-10">All Photos</h2>

    <!-- Slider Start -->
    <div class="relative max-w-4xl mx-auto mb-12">
        <div id="slider" class="overflow-hidden relative rounded-lg shadow-xl">
            <div class="flex transition-transform duration-500" id="slider-track">
                <?php foreach($gallery as $g): ?>
                    <div class="min-w-full h-64">
                        <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" 
                             alt="<?= htmlspecialchars($g['caption']) ?>" 
                             class="w-full h-full object-cover cursor-pointer" 
                             onclick="openPhoto('assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>')">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Prev/Next buttons -->
        <button onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❮</button>
        <button onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">❯</button>
    </div>
    <!-- Slider End -->

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-6xl mx-auto">
        <?php foreach($gallery as $g): ?>
            <div class="h-48 bg-gray-700 rounded-lg shadow-xl overflow-hidden">
                <img src="assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['caption']) ?>" class="w-full h-full object-cover cursor-pointer" onclick="openPhoto('assets/uploads/gallery/<?= htmlspecialchars($g['image']) ?>')">
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Optional Photo Modal -->
<div id="photo-modal" class="fixed inset-0 bg-black bg-opacity-80 hidden justify-center items-center z-50">
    <img id="photo-modal-img" src="" class="max-w-3xl max-h-[90%] rounded-lg shadow-2xl">
</div>

<script>
function openPhoto(src) {
    const modal = document.getElementById('photo-modal');
    const img = document.getElementById('photo-modal-img');
    img.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

document.getElementById('photo-modal').addEventListener('click', () => {
    const modal = document.getElementById('photo-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
});

// Slider JS
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

// Optional: Auto-slide every 5 seconds
setInterval(() => { nextSlide(); }, 5000);
</script>

<?php include 'includes/footer.php'; ?>
