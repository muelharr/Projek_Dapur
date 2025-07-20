<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dapur Kreatif</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="views/css/home.css">
</head>
<body class="home-page bg-gray-50 text-gray-800">
  <?php include 'header.php'; ?>

  <main class="container mx-auto px-4 py-6 main-content">
    <h2>Resep Terbaru</h2>

<?php
  require_once __DIR__ . '/../config/db.php';
  require_once __DIR__ . '/../controllers/RecipeController.php';
  $recipes = (new RecipeController($pdo))->index();
?>

<?php if ($recipes && count($recipes) > 0): ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($recipes as $r): ?>
      <a href="index.php?action=recipe_detail&id=<?= $r['id'] ?>"
         class="block bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden">

        <!-- Gambar -->
        <?php if (!empty($r['foto_url'])): ?>
          <img src="<?= htmlspecialchars($r['foto_url']) ?>"
               alt="<?= htmlspecialchars($r['judul']) ?>"
               class="w-full h-48 object-cover">
        <?php else: ?>
          <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
            <span class="text-gray-400">Gambar tidak tersedia</span>
          </div>
        <?php endif; ?>

        <!-- Konten -->
        <div class="p-4 flex flex-col h-32">
          <h3 class="text-lg font-medium line-clamp-2 mb-2">
            <?= htmlspecialchars($r['judul']) ?>
          </h3>
          <p class="text-sm text-gray-600 mt-auto">
            Kategori: <?= htmlspecialchars($r['kategori']) ?>
          </p>
        </div>

      </a>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="text-center text-gray-500">Belum ada resep yang disetujui.</p>
<?php endif; ?>

  </main>

  <?php include 'footer.php'; ?>

</body>
</html>