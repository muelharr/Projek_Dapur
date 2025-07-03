<?php include 'header.php'; ?>
<h2>Resep Terbaru</h2>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
$recipeController = new RecipeController($pdo);
$recipes = $recipeController->index();
if ($recipes && count($recipes) > 0):
    foreach ($recipes as $recipe): ?>
        <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
            <strong><?= htmlspecialchars($recipe['judul']) ?></strong><br>
            <em>Kategori: <?= htmlspecialchars($recipe['kategori']) ?> | Tingkat: <?= htmlspecialchars($recipe['tingkat_kesulitan']) ?></em><br>
            <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>">Lihat Detail</a>
        </div>
    <?php endforeach;
else:
    echo '<p>Belum ada resep yang disetujui.</p>';
endif;
?>
<?php include 'footer.php'; ?>
