<?php include 'header.php'; ?>
<h2>Dashboard Admin</h2>
<!-- Logout hanya di header -->
<div style="color:blue;">Selamat datang, Admin!</div>
<h3>Manajemen Resep (Menunggu Konfirmasi)</h3>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
$recipeController = new RecipeController($pdo);
if (isset($_GET['approve'])) {
    $recipeController->approve($_GET['approve']);
    header('Location: index.php?action=dashboard_admin');
    exit;
}
if (isset($_GET['reject'])) {
    $recipeController->reject($_GET['reject']);
    header('Location: index.php?action=dashboard_admin');
    exit;
}
if (isset($_GET['apply_edit'])) {
    $recipeController->applyEdit($_GET['apply_edit']);
    header('Location: index.php?action=dashboard_admin');
    exit;
}
if (isset($_GET['reject_edit'])) {
    $recipeController->rejectEdit($_GET['reject_edit']);
    header('Location: index.php?action=dashboard_admin');
    exit;
}
$pending = $recipeController->pending();
if ($pending && count($pending) > 0):
    foreach ($pending as $recipe): ?>
        <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
            <strong><?= htmlspecialchars($recipe['judul']) ?></strong> oleh <em><?= htmlspecialchars($recipe['nama']) ?></em><br>
            <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>">Lihat Detail</a>
            <?php if ($recipe['status'] === 'edit_pending' && $recipe['edit_data']): ?>
                | <a href="#" onclick="alert('Perubahan: ' + JSON.stringify(<?= json_encode($recipe['edit_data']) ?>)); return false;">Lihat Perubahan</a>
                | <a href="index.php?action=dashboard_admin&apply_edit=<?= $recipe['id'] ?>" style="color:green;">Setujui Edit</a>
                | <a href="index.php?action=dashboard_admin&reject_edit=<?= $recipe['id'] ?>" style="color:red;">Tolak Edit</a>
            <?php else: ?>
                | <a href="index.php?action=dashboard_admin&approve=<?= $recipe['id'] ?>" style="color:green;">Setujui</a>
                | <a href="index.php?action=dashboard_admin&reject=<?= $recipe['id'] ?>" style="color:red;">Tolak</a>
            <?php endif; ?>
        </div>
    <?php endforeach;
else:
    echo '<p>Tidak ada resep yang menunggu konfirmasi.</p>';
endif;
?>
<hr>
<h3>Semua Resep Terupload</h3>
<?php
// Proses hapus resep jika ada request
if (isset($_GET['delete_recipe'])) {
    $recipeController->delete($_GET['delete_recipe']);
    echo "<script>location.href='index.php?action=dashboard_admin';</script>";
    exit;
}
$all_recipes = $recipeController->getAll();
if ($all_recipes && count($all_recipes) > 0): ?>
    <div style="overflow-x:auto;">
    <table border="1" cellpadding="6" style="border-collapse:collapse; min-width:700px;">
        <tr style="background:#f0f0f0;">
            <th>ID</th>
            <th>Judul</th>
            <th>Oleh</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($all_recipes as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['judul']) ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td><?= date('d-m-Y H:i', strtotime($r['created_at'])) ?></td>
            <td>
                <a href="index.php?action=recipe_detail&id=<?= $r['id'] ?>">Detail</a>
                | <a href="index.php?action=dashboard_admin&delete_recipe=<?= $r['id'] ?>" onclick="return confirm('Yakin hapus resep ini beserta semua komentar dan data terkait?');" style="color:red;">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
<?php else:
    echo '<p>Tidak ada resep di database.</p>';
endif;
?>
<?php include 'footer.php'; ?>
