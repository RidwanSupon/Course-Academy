<?php
require_once 'config.php';
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<main class="pt-20 py-16 px-6 text-center bg-gray-50">
    <h2 class="text-4xl ilm-text-gold font-extrabold mb-10">All Photos</h2>
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
</script>

<?php include 'includes/footer.php'; ?>
