<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/CommentController.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../controllers/RatingController.php';
require_once __DIR__ . '/../controllers/FavoriteController.php';

// Tangani pengiriman rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nilai'], $_POST['recipe_id'], $_SESSION['user_id'])) {
    $recipe_id = intval($_POST['recipe_id']);
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['nilai']);

    // Redirect untuk menghindari pengiriman ulang formulir
    header('Location: index.php?action=recipe_detail&id=' . $recipe_id);
    exit;
}

// Tangani pengiriman komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['komentar'], $_POST['recipe_id'], $_SESSION['user_id'])) {
    $commentController = new CommentController($pdo);
    $commentController->add(
        intval($_POST['recipe_id']),
        $_SESSION['user_id'],
        trim($_POST['komentar'])
    );

    // Redirect untuk menghindari pengiriman ulang formulir
    header('Location: index.php?action=recipe_detail&id=' . $_POST['recipe_id']);
    exit;
}

// Ambil data resep
$id = $_GET['id'] ?? null;
$recipeController = new RecipeController($pdo);
$ratingController = new RatingController($pdo);
$favoriteController = new FavoriteController($pdo);

$recipe = $id ? $recipeController->detail($id) : null;
// Ambil semua foto gallery
require_once __DIR__ . '/../controllers/RecipePhotoController.php';
$photoController = new RecipePhotoController($pdo);
$gallery = $id ? $photoController->getByRecipe($id) : [];

// Ambil tags setelah $id dan $recipe sudah di-set
require_once __DIR__ . '/../models/Tag.php';
$tags = [];
if ($id && $recipe && class_exists('Tag') && method_exists('Tag', 'getByRecipe')) {
    $tags = Tag::getByRecipe($id);
}

// izin lihat
$can_view = $recipe &&
    ($recipe['status'] === 'approved'
     || ($_SESSION['user_id'] ?? null) === $recipe['user_id']
     || ($_SESSION['role'] ?? '') === 'admin');

// Data rating/fav/comments
$avg = $ratingController->getAverage($id) ?? ['avg_rating' => 0, 'jumlah' => 0];
$userRating = $_SESSION['user_id'] ? $ratingController->getUserRating($id, $_SESSION['user_id']) : null;
$isFav = $_SESSION['user_id'] ? $favoriteController->isFavorite($_SESSION['user_id'], $id) : false;
$comments = (new CommentController($pdo))->getByRecipe($id) ?? [];

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($recipe['judul'] ?? 'Detail Resep') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body, .prose, h1, h2, h3, h4, h5, h6, button, input, textarea, select, label, span, p, div {
      font-family: 'Poppins', Arial, sans-serif !important;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <?php include 'header.php'; ?>

  <?php if(!$can_view): ?>
    <main class="container mx-auto p-8 text-center">
      <p class="text-xl text-red-500">Resep tidak tersedia atau Anda tidak berhak melihat.</p>
    </main>
  <?php else: ?>
  <main class="container mx-auto px-4 py-8 space-y-12">

    <!-- Hero Image + Judul -->
    <div class="relative rounded-2xl overflow-hidden shadow-lg" style="max-width:700px;margin:auto;">
      <!-- Tombol favorit pojok kanan atas -->
      <form method="get" action="index.php" style="position:absolute;top:16px;right:16px;z-index:10;">
        <input type="hidden" name="action" value="toggle_favorite"/>
        <input type="hidden" name="id" value="<?= $id ?>"/>
        <button type="submit" title="Favoritkan" style="background:none;border:none;outline:none;cursor:pointer;">
          <?php if($isFav): ?>
            <!-- Bookmark filled -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="#f59e0b" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b" class="w-8 h-8 drop-shadow-lg">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 3.75A2.25 2.25 0 0 1 19.5 6v13.19a1 1 0 0 1-1.447.894L12 17.118l-6.053 2.966A1 1 0 0 1 4.5 19.19V6a2.25 2.25 0 0 1 2.25-2.25h10.5z" />
            </svg>
          <?php else: ?>
            <!-- Bookmark outline -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b" class="w-8 h-8 drop-shadow-lg">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 3.75A2.25 2.25 0 0 1 19.5 6v13.19a1 1 0 0 1-1.447.894L12 17.118l-6.053 2.966A1 1 0 0 1 4.5 19.19V6a2.25 2.25 0 0 1 2.25-2.25h10.5z" />
            </svg>
          <?php endif; ?>
        </button>
      </form>
      <?php if (!empty($gallery)): ?>
        <div id="carousel-container" style="position:relative;width:100%;height:320px;max-width:700px;margin:auto;">
          <?php foreach ($gallery as $i => $g): ?>
            <img src="<?= htmlspecialchars($g['file_path']) ?>" alt="Foto Resep" class="carousel-img" style="width:100%;height:320px;object-fit:cover;display:<?= $i === 0 ? 'block' : 'none' ?>;">
          <?php endforeach; ?>
          <button type="button" id="carousel-prev" style="position:absolute;top:50%;left:8px;transform:translateY(-50%);background:#fff8;border:none;border-radius:50%;width:32px;height:32px;font-size:18px;cursor:pointer;z-index:2;">&#8592;</button>
          <button type="button" id="carousel-next" style="position:absolute;top:50%;right:8px;transform:translateY(-50%);background:#fff8;border:none;border-radius:50%;width:32px;height:32px;font-size:18px;cursor:pointer;z-index:2;">&#8594;</button>
          <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 to-transparent p-6 pt-12" style="border-bottom-left-radius:12px;border-bottom-right-radius:12px;">
            <h1 class="text-3xl lg:text-4xl font-bold text-white drop-shadow-lg mb-2"><?= htmlspecialchars($recipe['judul']) ?></h1>
            <div class="flex flex-wrap items-center gap-2 drop-shadow mb-2">
              <span class="inline-block bg-orange-500 px-2 py-1 rounded-full text-xs font-medium">
                <?= htmlspecialchars($recipe['kategori']) ?>
              </span>
              <span class="text-xs text-white">
                <?= ucfirst(htmlspecialchars($recipe['tingkat_kesulitan'])) ?>
              </span>
            </div>
            <?php if (!empty($tags)): ?>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($tags as $tag): ?>
                <span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">#<?= htmlspecialchars($tag['nama']) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <script>
        const imgs = document.querySelectorAll('.carousel-img');
        let idx = 0;
        function showImg(i) {
          imgs.forEach((img, j) => img.style.display = (i === j ? 'block' : 'none'));
        }
        document.getElementById('carousel-prev').onclick = function() {
          idx = (idx - 1 + imgs.length) % imgs.length;
          showImg(idx);
        };
        document.getElementById('carousel-next').onclick = function() {
          idx = (idx + 1) % imgs.length;
          showImg(idx);
        };
        </script>
      <?php elseif(!empty($recipe['foto_url'])): ?>
        <div style="position:relative;width:100%;height:320px;max-width:700px;margin:auto;">
          <img src="<?= htmlspecialchars($recipe['foto_url']) ?>"
               alt="<?= htmlspecialchars($recipe['judul']) ?>"
               style="width:100%;height:320px;object-fit:cover;display:block;">
          <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 to-transparent p-6 pt-12" style="border-bottom-left-radius:12px;border-bottom-right-radius:12px;">
            <h1 class="text-3xl lg:text-4xl font-bold text-white drop-shadow-lg mb-2"><?= htmlspecialchars($recipe['judul']) ?></h1>
            <div class="flex flex-wrap items-center gap-2 drop-shadow mb-2">
              <span class="inline-block bg-orange-500 px-2 py-1 rounded-full text-xs font-medium">
                <?= htmlspecialchars($recipe['kategori']) ?>
              </span>
              <span class="text-xs text-white">
                <?= ucfirst(htmlspecialchars($recipe['tingkat_kesulitan'])) ?>
              </span>
            </div>
            <?php if (!empty($tags)): ?>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($tags as $tag): ?>
                <span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">#<?= htmlspecialchars($tag['nama']) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($recipe && $recipe['status'] === 'approved' && !empty($recipe['created_at'])): ?>
      <div class="text-center text-gray-500 text-sm mt-2 mb-4" style="max-width:700px;margin:auto;">
        Diupload pada: <?= date('d M Y, H:i', strtotime($recipe['created_at'])) ?>
      </div>
    <?php endif; ?>

    <!-- Grid Konten -->
    <div class="space-y-8 max-w-2xl mx-auto">
      <!-- Deskripsi -->
      <article class="prose max-w-none">
        <h2>Deskripsi</h2>
        <p><?= nl2br(htmlspecialchars($recipe['deskripsi'])) ?></p>
      </article>
      <!-- Bahan -->
      <article class="prose max-w-none">
        <h2>Bahan</h2>
        <p><?= nl2br(htmlspecialchars($recipe['bahan'])) ?></p>
      </article>
      <!-- Langkah -->
      <article class="prose max-w-none">
        <h2>Langkah</h2>
        <p><?= nl2br(htmlspecialchars($recipe['langkah'])) ?></p>
      </article>
      <!-- Rating -->
      <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
        <h3 class="text-xl font-semibold">Rating</h3>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-700"><strong><?= $avg['jumlah'] ?></strong> Rating</p>
            <p class="text-2xl font-bold"><?= number_format($avg['avg_rating'] ?? 0, 1) ?>/5</p>
          </div>
          <div class="flex items-center space-x-2">
            <?php if($_SESSION['user_id']): ?>
              <!-- Form beri rating -->
              <form method="post" action="index.php?action=add_rating" class="flex items-center space-x-1">
                <input type="hidden" name="recipe_id" value="<?= $id ?>"/>
                <?php for($i = 5; $i >= 1; $i--): ?>
                  <input type="radio" id="star<?= $i ?>" name="nilai" value="<?= $i ?>" class="hidden"
                         <?= isset($userRating['nilai']) && $userRating['nilai'] == $i ? 'checked' : ''; ?>/>
                  <label for="star<?= $i ?>" class="text-2xl cursor-pointer"
                         style="color: <?= isset($userRating['nilai']) && $userRating['nilai'] >= $i ? '#f59e0b' : '#ccc' ?>;">&#9733;</label>
                <?php endfor; ?>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                  Berikan Rating
                </button>
              </form>
            <?php else: ?>
              <p class="text-gray-500 text-sm">Login untuk rate</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- Komentar -->
      <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
        <h3 class="text-xl font-semibold">Komentar</h3>
        <!-- Form komentar -->
        <?php if($_SESSION['user_id']): ?>
          <form method="post" action="index.php?action=recipe_detail&id=<?= $id ?>" class="space-y-2">
            <input type="hidden" name="recipe_id" value="<?= $id ?>"/>
            <textarea name="komentar" rows="3"
                      class="w-full border border-gray-300 rounded-lg p-3"
                      placeholder="Tulis komentar..." required></textarea>
            <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
              Kirim Komentar
            </button>
          </form>
        <?php else: ?>
          <p class="text-gray-500 text-sm">Login untuk menulis komentar.</p>
        <?php endif; ?>
        <!-- Daftar komentar -->
        <?php if(count($comments) > 0): ?>
          <div class="space-y-4 max-h-64 overflow-auto pr-2">
            <?php foreach($comments as $c): ?>
              <div class="border-b last:border-none pb-3">
                <div class="flex justify-between text-sm text-gray-500">
                  <span><?= htmlspecialchars($c['nama']) ?></span>
                  <span><?= date('d-m-Y H:i', strtotime($c['created_at'])) ?></span>
                </div>
                <p class="mt-1 text-gray-700"><?= htmlspecialchars($c['komentar']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-gray-500 text-sm">Belum ada komentar.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>
  <?php include 'footer.php'; ?>
  <?php endif; ?>
</body>
</html>
