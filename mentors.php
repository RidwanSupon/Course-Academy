<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch all mentors
$mentors = $pdo->query("SELECT * FROM mentors ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Expert Mentors</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                'primary': '#4f46e5', // Indigo-600
                'secondary': '#6366f1', // Indigo-500
            },
            keyframes: {
                slideInRight: {
                    '0%': { transform: 'translateX(50px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' }
                }
            },
            animation: {
                'slide-in-right': 'slideInRight 0.7s ease-out forwards'
            }
        }
    }
}
</script>
</head>
<body class="bg-gray-50">

<?php include 'includes/header.php'; ?>

<main class="pt-16">
<section id="mentor-list" class="py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-8">
    <?php if (!empty($mentors)): ?>
        <?php foreach($mentors as $mentor): ?>
            <div 
                class="mentor-card bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col cursor-pointer animate-slide-in-right w-64 h-[400px]"
                data-id="<?= e($mentor['id']) ?>"
                data-name="<?= e($mentor['name']) ?>"
                data-specialization="<?= e($mentor['specialization']) ?>"
                data-bio="<?= e($mentor['bio'] ?? 'No biography available.') ?>"
                data-email="<?= e($mentor['email'] ?? '') ?>"
                data-phone="<?= e($mentor['phone'] ?? '') ?>"
                data-photo="assets/uploads/mentors/<?= e($mentor['photo']) ?>"
                data-has-photo="<?= $mentor['photo'] ? 'true' : 'false' ?>"
            >
                <div class="p-6 flex flex-col items-center text-center flex-grow">
                    <?php if ($mentor['photo']): ?>
                        <img 
                            src="assets/uploads/mentors/<?= e($mentor['photo']) ?>" 
                            alt="<?= e($mentor['name']) ?> Photo" 
                            class="w-28 h-28 rounded-full object-cover mb-4 ring-4 ring-secondary/50 ring-offset-2"
                        >
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-gray-400 text-sm font-medium border border-gray-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    <?php endif; ?>

                    <h2 class="text-xl font-bold text-gray-900 mb-1 leading-tight"><?= e($mentor['name']) ?></h2>
                    <p class="text-primary font-semibold text-sm uppercase tracking-wider mb-3"><?= e($mentor['specialization']) ?></p>

                    <?php if($mentor['bio']): ?>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?= e(substr($mentor['bio'], 0, 150) . (strlen($mentor['bio']) > 150 ? '...' : '')) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 text-center">
                    <span class="text-primary font-medium text-sm hover:underline">View Full Profile &rarr;</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-10 w-full">
            <p class="text-xl text-gray-500 font-medium">We are currently updating our mentor list. Please check back soon!</p>
        </div>
    <?php endif; ?>
</div>

    </div>
</section>
</main>

<?php include 'includes/footer.php'; ?>

<!-- Modal (unchanged) -->
<div id="mentor-detail-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-xl mx-auto transform transition-all overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="p-6 sm:p-8 relative">
            <button id="close-modal" class="sticky top-0 float-right text-gray-400 hover:text-gray-600 transition -mt-2 mb-4" aria-label="Close modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="text-center mb-6">
                <img id="modal-photo" src="" alt="Mentor Photo" class="w-28 h-28 rounded-full object-cover mx-auto mb-4 ring-4 ring-primary/50 ring-offset-2">
                <h3 id="modal-name" class="text-3xl font-bold text-gray-900 mb-1"></h3>
                <p id="modal-specialization" class="text-xl text-primary font-semibold uppercase tracking-wider"></p>
            </div>

            <div class="text-left mb-6 border-t pt-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">About the Mentor:</h4>
                <p id="modal-bio" class="text-gray-600 leading-relaxed whitespace-pre-line"></p>
            </div>

            <div class="text-left bg-gray-50 p-4 rounded-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Contact & Details:</h4>
                <div class="space-y-2 text-sm">
                    <div id="modal-email-container" class="flex items-center text-gray-700"></div>
                    <div id="modal-phone-container" class="flex items-center text-gray-700"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Modal logic unchanged
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('mentor-detail-modal');
    const closeModalButton = document.getElementById('close-modal');
    const mentorCards = document.querySelectorAll('.mentor-card');

    const modalName = document.getElementById('modal-name');
    const modalSpecialization = document.getElementById('modal-specialization');
    const modalBio = document.getElementById('modal-bio');
    const modalPhoto = document.getElementById('modal-photo');
    const modalEmailContainer = document.getElementById('modal-email-container');
    const modalPhoneContainer = document.getElementById('modal-phone-container');

    const showModal = (mentor) => {
        modalName.textContent = mentor.name;
        modalSpecialization.textContent = mentor.specialization;
        modalBio.innerHTML = mentor.bio.replace(/\n/g, '<br>');

        if (mentor.hasPhoto === 'true') {
            modalPhoto.src = mentor.photo;
        } else {
            modalPhoto.src = 'assets/images/student-placeholder.jpg';
        }

        if (mentor.email) {
            modalEmailContainer.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>Email: <a href="mailto:${mentor.email}" class="ml-1 text-primary hover:underline">${mentor.email}</a>`;
            modalEmailContainer.classList.remove('hidden');
        } else modalEmailContainer.classList.add('hidden');

        if (mentor.phone) {
            modalPhoneContainer.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>Phone: <a href="tel:${mentor.phone}" class="ml-1 text-primary hover:underline">${mentor.phone}</a>`;
            modalPhoneContainer.classList.remove('hidden');
        } else modalPhoneContainer.classList.add('hidden');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const hideModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    mentorCards.forEach(card => {
        card.addEventListener('click', () => showModal(card.dataset));
    });

    closeModalButton.addEventListener('click', hideModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) hideModal();
    });
});
</script>
</body>
</html>
