<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/style.css">
</head>
<body>
<?php
include 'header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../controllers/RatingController.php';
$recipeController = new RecipeController($pdo);
$ratingController = new RatingController($pdo);

// Ambil filter
$q = $_GET['q'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$tingkat = $_GET['tingkat'] ?? '';
$min_rating = $_GET['min_rating'] ?? '';

// Ambil data kategori unik
$kategori_list = $pdo->query("SELECT DISTINCT kategori FROM recipes WHERE status='approved'")->fetchAll(PDO::FETCH_COLUMN);
$tingkat_list = $pdo->query("SELECT DISTINCT tingkat_kesulitan FROM recipes WHERE status='approved'")->fetchAll(PDO::FETCH_COLUMN);

// Query pencarian
$recipes = $recipeController->search($q, $kategori, $tingkat, $min_rating);
?>
<h2>Cari Resep</h2>
<form method="get" action="index.php">
    <input type="hidden" name="action" value="search">
    <input type="text" name="q" placeholder="Cari judul/deskripsi..." value="<?= htmlspecialchars($q) ?>" style="width:200px;">
    <select name="kategori">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategori_list as $k): ?>
            <option value="<?= htmlspecialchars($k) ?>" <?= $kategori==$k?'selected':'' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="tingkat">
        <option value="">Semua Tingkat</option>
        <?php foreach ($tingkat_list as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= $tingkat==$t?'selected':'' ?>><?= htmlspecialchars($t) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="min_rating">
        <option value="">Semua Rating</option>
        <option value="4" <?= $min_rating=='4'?'selected':'' ?>>4+ bintang</option>
        <option value="3" <?= $min_rating=='3'?'selected':'' ?>>3+ bintang</option>
        <option value="2" <?= $min_rating=='2'?'selected':'' ?>>2+ bintang</option>
        <option value="1" <?= $min_rating=='1'?'selected':'' ?>>1+ bintang</option>
    </select>
    <button type="submit">Cari</button>
</form>
<hr>
<?php if ($recipes && count($recipes) > 0): ?>
    <div class="recipe-grid">
    <?php foreach ($recipes as $recipe):
        $avg = $ratingController->getAverage($recipe['id']);
    ?>
    <div class="recipe-card">
        <?php if (!empty($recipe['foto_url'])): ?>
            <img src="<?= htmlspecialchars($recipe['foto_url']) ?>" alt="Foto <?= htmlspecialchars($recipe['judul']) ?>" class="recipe-card-img">
        <?php else: ?>
            <div class="recipe-card-img-placeholder"><span>Tidak Ada Gambar</span></div>
        <?php endif; ?>
        <div class="recipe-card-content">
            <strong class="recipe-card-title"><?= htmlspecialchars($recipe['judul']) ?></strong>
            <em class="recipe-card-meta">Kategori: <?= htmlspecialchars($recipe['kategori']) ?></em>
            <em class="recipe-card-meta">Rating: <?= $avg && $avg['avg_rating'] ? number_format($avg['avg_rating'],1).' / 5' : 'Baru' ?></em>
            <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" class="recipe-card-link">Lihat Detail</a>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Tidak ada resep ditemukan.</p>
<?php endif; ?>
<?php include 'footer.php'; ?>
</body>
</html>
