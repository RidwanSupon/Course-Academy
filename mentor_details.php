<?php
// mentor_details.php - Displays the full profile of a specific mentor/instructor

require_once __DIR__ . '/config.php';
// We rely on this file (or config.php) to load the global functions like e()
require_once __DIR__ . '/includes/functions.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;

// 1. Get Mentor ID from URL
$mentor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$mentor = null;
$mentorCourses = [];

if ($mentor_id > 0) {
    try {
        // 2. Fetch Mentor Details
        $stmtMentor = $pdo->prepare("SELECT * FROM mentors WHERE id = ? LIMIT 1");
        $stmtMentor->execute([$mentor_id]);
        $mentor = $stmtMentor->fetch(PDO::FETCH_ASSOC);

        // 3. Fetch all active courses taught by this mentor
        if ($mentor) {
            $stmtCourses = $pdo->prepare("
                SELECT id, title, short_desc, price, duration, gender
                FROM courses 
                WHERE mentor_id = ? AND active = 1
                ORDER BY title ASC
            ");
            $stmtCourses->execute([$mentor_id]);
            $mentorCourses = $stmtCourses->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        // Handle database error if necessary
    }
}

// 4. Redirect if mentor not found
if (!$mentor) {
    header('Location: index.php');
    exit;
}

// Tailwind color configuration (assuming you have these defined globally)
$ilm_blue = '#0A1C3C';
$ilm_gold = '#D4AF37';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($mentor['name']) ?> - Instructor Profile</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'ilm-blue': '<?= $ilm_blue ?>',
                    'ilm-gold': '<?= $ilm_gold ?>'
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                }
            }
        }
    }
    </script>
</head>

<body class="bg-gray-50 font-sans">
<?php include 'includes/header.php'; // Include your site header ?>

<main class="pt-32 px-4 md:px-6 max-w-7xl mx-auto">

    <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 mb-12 border-t-8 border-ilm-blue">
        
        <div class="flex flex-col md:flex-row items-center md:items-start space-y-8 md:space-y-0 md:space-x-10 border-b border-gray-200 pb-8 mb-8">
            
            <img src="assets/uploads/mentors/<?= e($mentor['photo'] ?? 'default.png') ?>" 
                 alt="<?= e($mentor['name']) ?>" 
                 class="w-40 h-40 object-cover rounded-full ring-8 ring-ilm-gold/50 shadow-xl flex-shrink-0">
            
            <div>
                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 block">Instructor Profile</span>
                <h1 class="text-4xl md:text-5xl font-black text-ilm-blue mb-2 leading-tight"><?= e($mentor['name']) ?></h1>
                <h2 class="text-xl md:text-2xl font-semibold text-ilm-gold mb-4">
                    <?= e($mentor['specialization'] ?? 'Expert Educator') ?>
                </h2>
                
                <div class="flex items-center space-x-4 text-gray-600 text-sm">
                    <?php if (!empty($mentor['email'])): ?><span class="flex items-center"><span class="material-icons text-lg mr-1">mail</span> <?= e($mentor['email']) ?></span><?php endif; ?>
                    <?php if (!empty($mentor['phone'])): ?><span class="flex items-center"><span class="material-icons text-lg mr-1">call</span> <?= e($mentor['phone']) ?></span><?php endif; ?>
                </div>
            </div>
        </div>

        <section class="mb-10">
            <h3 class="text-3xl font-extrabold text-ilm-blue mb-4 pb-2 border-b-2 border-ilm-gold inline-block">Biography</h3>
            <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-line">
                <?= nl2br(e($mentor['bio'] ?? 'No detailed biography provided yet.')) ?>
            </p>
        </section>

        <section>
            <h3 class="text-3xl font-extrabold text-ilm-blue mb-6 pb-2 border-b-2 border-ilm-gold inline-block">
                Courses Taught (<?= count($mentorCourses) ?>)
            </h3>
            
            <?php if (empty($mentorCourses)): ?>
                <div class="bg-gray-100 p-6 rounded-xl text-center text-gray-600 font-medium">
                    This instructor is not currently teaching any active courses.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($mentorCourses as $course): ?>
                        <a href="course.php?id=<?= intval($course['id']) ?>" class="block bg-gray-50 p-5 rounded-xl border border-gray-200 hover:border-ilm-gold transition duration-200 shadow-md hover:shadow-lg">
                            <div class="flex justify-between items-center">
                                <h4 class="text-xl font-bold text-ilm-blue group-hover:text-indigo-700">
                                    <?= e($course['title']) ?>
                                </h4>
                                <span class="text-lg font-black text-ilm-gold flex-shrink-0">
                                    <?= $course['price'] > 0 ? '৳ ' . number_format($course['price'], 0) : 'FREE' ?>
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1 mb-3"><?= e($course['short_desc']) ?></p>
                            <div class="flex items-center space-x-4 text-xs text-gray-500 pt-2 border-t border-gray-100">
                                <span><span class="font-semibold">Duration:</span> <?= e($course['duration'] ?? 'N/A') ?></span>
                                <span><span class="font-semibold">For:</span> <?= e($course['gender']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>

</main>

<?php include 'includes/footer.php'; // Include your site footer ?>

</body>
</html>