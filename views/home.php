<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="main-content">
    <h2>Resep Terbaru</h2>
    <?php
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../controllers/RecipeController.php';
    $recipeController = new RecipeController($pdo);
    $recipes = $recipeController->index();

    if ($recipes && count($recipes) > 0):
        echo '<div class="recipe-grid">';
        foreach ($recipes as $recipe): ?>
            <div class="recipe-card">
                <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" style="text-decoration:none; color:inherit; display:block; height:100%;">
                    <?php if (!empty($recipe['foto_url'])): ?>
                        <img src="<?= htmlspecialchars($recipe['foto_url']) ?>" alt="<?= htmlspecialchars($recipe['judul']) ?>" class="recipe-card-img">
                    <?php else: ?>
                        <div class="recipe-card-img-placeholder"><span>Tidak Ada Gambar</span></div>
                    <?php endif; ?>
                    <div class="recipe-card-content">
                        <p class="recipe-card-title"><?= htmlspecialchars($recipe['judul']) ?></p>
                        <p class="recipe-card-meta">Kategori: <?= htmlspecialchars($recipe['kategori']) ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach;
        echo '</div>';
    else:
        echo '<p>Belum ada resep yang disetujui.</p>';
    endif;
    ?>
</main>
<?php include 'footer.php'; ?>
</body>
</html>