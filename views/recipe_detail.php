<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/CommentController.php';

// ↓——— Tangani kirim komentar ———↓
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_SESSION['user_id'])
    && !empty($_POST['komentar'])
    && !empty($_POST['recipe_id'])
) {
    $cc = new CommentController($pdo);
    $cc->add(
        intval($_POST['recipe_id']),
        $_SESSION['user_id'],
        trim($_POST['komentar'])
    );
    // Redirect kembali ke halaman detail
    header('Location: index.php?action=recipe_detail&id=' . intval($_POST['recipe_id']));
    exit;
}
// ↑——— END logika POST ———↑

// Sekarang baru load controller resep, rating, dsb.
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../controllers/RatingController.php';
require_once __DIR__ . '/../controllers/FavoriteController.php';
$recipeController   = new RecipeController($pdo);
$ratingController   = new RatingController($pdo);
$favoriteController = new FavoriteController($pdo);

// Ambil data resep
$id     = $_GET['id'] ?? null;
$recipe = $id ? $recipeController->detail($id) : null;

// izin lihat
$can_view = $recipe &&
    ($recipe['status']==='approved'
     || ($_SESSION['user_id']??null)==$recipe['user_id']
     || ($_SESSION['role']??'')==='admin');

// data rating/fav/comments
$avg         = $ratingController->getAverage($id) ?? ['avg_rating'=>0,'jumlah'=>0];
$userRating  = $_SESSION['user_id'] ? $ratingController->getUserRating($id, $_SESSION['user_id']) : null;
$isFav       = $_SESSION['user_id'] ? $favoriteController->isFavorite($_SESSION['user_id'], $id) : false;
$comCtrl     = new CommentController($pdo);
$comments    = $comCtrl->getByRecipe($id) ?? [];

?><!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($recipe['judul']??'Detail Resep') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="relative rounded-2xl overflow-hidden shadow-lg">
      <?php if(!empty($recipe['foto_url'])): ?>
        <img src="<?= htmlspecialchars($recipe['foto_url']) ?>"
             alt="<?= htmlspecialchars($recipe['judul']) ?>"
             class="w-full h-96 object-cover"/>
      <?php endif; ?>
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
      <div class="absolute bottom-6 left-6 text-white">
        <h1 class="text-4xl lg:text-5xl font-bold drop-shadow-lg"><?= htmlspecialchars($recipe['judul']) ?></h1>
        <p class="mt-2 text-sm drop-shadow">
          <span class="inline-block bg-orange-500 px-2 py-1 rounded-full text-xs font-medium">
            <?= htmlspecialchars($recipe['kategori']) ?>
          </span>
          <span class="ml-2 text-xs">
            <?= ucfirst(htmlspecialchars($recipe['tingkat_kesulitan'])) ?>
          </span>
        </p>
      </div>
    </div>

    <!-- Grid Konten -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Konten Utama -->
      <section class="lg:col-span-2 space-y-8">
        <article class="prose max-w-none">
          <h2>Deskripsi</h2>
          <p><?= nl2br(htmlspecialchars($recipe['deskripsi'])) ?></p>
        </article>
        <article class="prose max-w-none">
          <h2>Bahan</h2>
          <p><?= nl2br(htmlspecialchars($recipe['bahan'])) ?></p>
        </article>
        <article class="prose max-w-none">
          <h2>Langkah</h2>
          <p><?= nl2br(htmlspecialchars($recipe['langkah'])) ?></p>
        </article>
      </section>

      <!-- Sidebar: Rating & Komentar -->
      <aside class="space-y-8">

        <!-- Card Rating & Favorit -->
        <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
          <h3 class="text-xl font-semibold">Rating & Favorit</h3>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-700"><strong><?= $avg['jumlah'] ?></strong> Rating</p>
              <p class="text-2xl font-bold"><?= number_format($avg['avg_rating'],1) ?>/5</p>
            </div>
            <div class="flex items-center space-x-2">
              <?php if($_SESSION['user_id']): ?>
                <!-- Form beri rating -->
                <form method="post" action="index.php?action=add_rating" class="flex items-center space-x-1">
                  <input type="hidden" name="recipe_id" value="<?= $id ?>"/>
                  <?php for($i=5;$i>=1;$i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="nilai" value="<?= $i ?>" class="hidden"
                      <?= ($userRating['nilai']??0)==$i?'checked':'';?>/>
                    <label for="star<?= $i ?>" class="text-2xl cursor-pointer"
                           style="color:<?= ($userRating['nilai']??0)>=$i?'#f59e0b':'#ccc' ?>;">&#9733;</label>
                  <?php endfor;?>
                </form>
                <!-- Tombol favorit -->
                <form method="get" action="index.php">
                  <input type="hidden" name="action" value="toggle_favorite"/>
                  <input type="hidden" name="id" value="<?= $id ?>"/>
                  <button type="submit"
                          class="text-2xl <?= $isFav?'text-yellow-400':'text-gray-300' ?>">
                    &#9733;
                  </button>
                </form>
              <?php else: ?>
                <p class="text-gray-500 text-sm">Login untuk rate & favorit</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Card Komentar -->
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
          <?php if(count($comments)>0): ?>
            <div class="space-y-4 max-h-64 overflow-auto pr-2">
              <?php foreach($comments as $c): ?>
                <div class="border-b last:border-none pb-3">
                  <div class="flex justify-between text-sm text-gray-500">
                    <span><?= htmlspecialchars($c['nama']) ?></span>
                    <span><?= date('d-m-Y H:i',strtotime($c['created_at'])) ?></span>
                  </div>
                  <p class="mt-1 text-gray-700"><?= htmlspecialchars($c['komentar']) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-gray-500 text-sm">Belum ada komentar.</p>
          <?php endif; ?>
        </div>

      </aside>

    </div>
  </main>
  <?php include 'footer.php'; ?>
  <?php endif; ?>
</body>
</html>
