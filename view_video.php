<?php
require_once 'config.php';
$id = intval($_GET['id'] ?? 0);
$v = $pdo->prepare("SELECT * FROM videos WHERE id=?");
$v->execute([$id]);
$video = $v->fetch();
if(!$video) { die('Not found'); }
function to_embed($url) {
    // handle YouTube watch?v= and short youtu.be and embed vimeo etc. This is minimal.
    if(strpos($url,'youtube.com/watch') !== false){
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        if(!empty($q['v'])) return 'https://www.youtube.com/embed/' . $q['v'];
    }
    if(strpos($url,'youtu.be') !== false){
        $parts = explode('/', $url);
        return 'https://www.youtube.com/embed/' . end($parts);
    }
    return $url;
}
$embed = to_embed($video['url']);
?>
<!doctype html><html><head><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-50 p-6">
<div class="max-w-4xl mx-auto bg-white p-4 rounded shadow">
  <h2 class="text-xl font-bold"><?= e($video['title']) ?></h2>
  <div class="mt-4">
    <iframe class="w-full" style="height:420px" src="<?= e($embed) ?>" frameborder="0" allowfullscreen></iframe>
  </div>
</div>
</body></html>
