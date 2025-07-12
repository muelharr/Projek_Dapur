<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<h2>Dashboard</h2>
<!-- Logout hanya di header -->
<br><br>
<a href="index.php?action=add_recipe" style="padding:8px 16px; background:#28a745; color:#fff; text-decoration:none; border-radius:4px;">Upload Resep</a>

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../controllers/FavoriteController.php';
$recipeController = new RecipeController($pdo);
$favoriteController = new FavoriteController($pdo);
$user_id = $_SESSION['user_id'];
// Notifikasi resep pending
$my_pending = $recipeController->getByUser($user_id);
$pending_count = 0;
if ($my_pending && count($my_pending) > 0) {
    foreach ($my_pending as $r) {
        if ($r['status'] === 'pending') {
            $pending_count++;
        }
    }
}
if ($pending_count > 0) {
    echo '<div style="background:#fff3cd;color:#856404;padding:10px 15px;border:1px solid #ffeeba;border-radius:5px;margin-bottom:20px;">';
    echo 'Anda memiliki <b>' . $pending_count . '</b> resep yang masih <b>menunggu konfirmasi admin</b>.';
    echo '</div>';
}
?>

<h3>Resep Saya</h3>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../controllers/FavoriteController.php';
$recipeController = new RecipeController($pdo);
$favoriteController = new FavoriteController($pdo);
$user_id = $_SESSION['user_id'];
// Tampilkan semua resep approved di dashboard user (bisa milik user lain)
$all_approved = $recipeController->index();
$favorites = $favoriteController->getByUser($user_id);
$favorite_ids = array_map(function($fav){ return $fav['recipe_id']; }, $favorites);
if ($all_approved && count($all_approved) > 0):
    foreach ($all_approved as $recipe): ?>
        <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
            <strong><?= htmlspecialchars($recipe['judul']) ?></strong><br>
            <em>Oleh: <?= htmlspecialchars($recipe['user_id'] == $_SESSION['user_id'] ? 'Saya' : ($recipe['nama'] ?? ('User #' . $recipe['user_id'])) ) ?></em><br>
            <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>">Lihat Detail</a>
            <?php if ($recipe['user_id'] == $_SESSION['user_id']): ?>
                | <a href="index.php?action=edit_recipe&id=<?= urlencode($recipe['id']) ?>" style="color:orange;">Edit</a>
            <?php endif; ?>
            <?php if (in_array($recipe['id'], $favorite_ids)): ?>
                <span style="color:#ffb700; margin-left:10px;">★ Favorit</span>
            <?php endif; ?>
        </div>
    <?php endforeach;
else:
    echo '<p>Belum ada resep yang disetujui.</p>';
endif;
?>

<h3>Resep Favorit Saya</h3>
<?php
if ($favorites && count($favorites) > 0):
    foreach ($favorites as $fav):
        $fav_recipe = $recipeController->detail($fav['recipe_id']);
        if ($fav_recipe && $fav_recipe['status'] === 'approved'):
?>
        <div style="border:1px solid #ffb700; margin-bottom:10px; padding:10px; background:#fffbe6;">
            <strong><?= htmlspecialchars($fav_recipe['judul']) ?></strong><br>
            <a href="index.php?action=recipe_detail&id=<?= $fav_recipe['id'] ?>">Lihat Detail</a>
        </div>
<?php
        endif;
    endforeach;
else:
    echo '<p>Belum ada resep favorit.</p>';
endif;
?>
<?php include 'footer.php'; ?>
</body>
</html>
