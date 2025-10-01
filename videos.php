<?php
require_once 'config.php';
$videos = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<main class="pt-20 py-16 px-6 text-center bg-gray-50">
    <h2 class="text-4xl ilm-text-gold font-extrabold mb-10">All Videos</h2>
    <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
        <?php foreach($videos as $video): 
            preg_match("/(?:youtube\.com\/.*v=|youtu\.be\/)([^&\n]+)/", $video['url'], $matches);
            $youtube_id = $matches[1] ?? null;
        ?>
            <?php if($youtube_id): ?>
                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-2xl bg-gray-900">
                    <iframe class="w-full h-full" 
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($youtube_id) ?>" 
                            title="<?= htmlspecialchars($video['title']) ?>" 
                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
