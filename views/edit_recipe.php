<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/edit_r.css">
</head>
<body>
<?php include 'header.php'; ?>
<h2>Edit Resep</h2>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
require_once __DIR__ . '/../models/Tag.php';
$recipeController = new RecipeController($pdo);
$id = $_GET['id'] ?? null;
$edit_success = null;
if (!$id) {
    echo '<p>ID resep tidak ditemukan.</p>';
    include 'footer.php';
    exit;
}
$recipe = $recipeController->detail($id);
if (!$recipe || $recipe['user_id'] != $_SESSION['user_id']) {
    echo '<p>Anda tidak berhak mengedit resep ini.</p>';
    include 'footer.php';
    exit;
}
if ($recipe['status'] !== 'approved' && $recipe['status'] !== 'rejected') {
    echo '<div style="color:orange;">Resep hanya bisa diedit jika sudah disetujui atau ditolak admin.<br>Status saat ini: <b>' . htmlspecialchars($recipe['status']) . '</b></div>';
    include 'footer.php';
    exit;
}
// Ambil tags dan tag yang terpilih
$tags = Tag::getAll();
$selected_tags = Tag::getByRecipe($id);
$selected_tag_ids = array_map(function($t){return $t['id'];}, $selected_tags);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $bahan = $_POST['bahan'] ?? '';
    $langkah = $_POST['langkah'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $tingkat_kesulitan = $_POST['tingkat_kesulitan'] ?? '';
    $foto_url = $recipe['foto_url'];
    // Foto multi
    $foto_paths = [];
    if (!empty($_FILES['foto']['name'][0])) {
        foreach ($_FILES['foto']['tmp_name'] as $i => $tmp) {
            if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'][$i], PATHINFO_EXTENSION);
                $newname = 'resep_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $target = __DIR__ . '/../uploads/' . $newname;
                if (move_uploaded_file($tmp, $target)) {
                    $foto_paths[] = 'uploads/' . $newname;
                }
            }
        }
        if ($foto_paths) {
            $foto_url = $foto_paths[0]; // Simpan satu utama
        }
    }
    $data = [
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'bahan' => $bahan,
        'langkah' => $langkah,
        'foto_url' => $foto_url,
        'kategori' => $kategori,
        'tingkat_kesulitan' => $tingkat_kesulitan
    ];
    if ($recipeController->updateRequest($id, $data)) {
        // Simpan tags
        $pdo->prepare('DELETE FROM recipe_tags WHERE recipe_id=?')->execute([$id]);
        if (!empty($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag_id) {
                $pdo->prepare('INSERT INTO recipe_tags (recipe_id, tag_id) VALUES (?, ?)')->execute([$id, $tag_id]);
            }
        }
        echo "<script>location.href='index.php?action=dashboard';</script>";
        exit;
    } else {
        $edit_success = false;
    }
}
?>
<?php if ($edit_success === true): ?>
    <div style="color:green;">Perubahan berhasil diajukan! Menunggu konfirmasi admin.</div>
<?php elseif ($edit_success === false): ?>
    <div style="color:red;">Gagal mengajukan perubahan.</div>
<?php endif; ?>
<form method="post" action="index.php?action=edit_recipe&amp;id=<?= urlencode($id) ?>" enctype="multipart/form-data" style="max-height:480px; overflow-y:auto; padding-right:8px;">
    <label>Judul:</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($recipe['judul']) ?>" required><br>
    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" required><?= htmlspecialchars($recipe['deskripsi']) ?></textarea><br>
    <div style="display:flex; gap:10px; width:100%;">
        <div style="flex:1; min-width:0;">
            <label>Bahan:</label>
            <textarea name="bahan" required style="min-height:60px;"><?= htmlspecialchars($recipe['bahan']) ?></textarea>
        </div>
        <div style="flex:1; min-width:0;">
            <label>Langkah:</label>
            <textarea name="langkah" required style="min-height:60px;"><?= htmlspecialchars($recipe['langkah']) ?></textarea>
        </div>
    </div>
    <label>Kategori:</label><br>
    <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Makanan" <?= ($recipe['kategori'] == 'Makanan') ? 'selected' : '' ?>>Makanan</option>
        <option value="Minuman" <?= ($recipe['kategori'] == 'Minuman') ? 'selected' : '' ?>>Minuman</option>
        <option value="Cemilan" <?= ($recipe['kategori'] == 'Cemilan') ? 'selected' : '' ?>>Cemilan</option>
        <option value="Kue" <?= ($recipe['kategori'] == 'Kue') ? 'selected' : '' ?>>Kue</option>
    </select><br>
    <label>Tags (opsional):</label><br>
    <div class="tags-checkboxes">
        <?php foreach ($tags as $tag): ?>
            <label style="display:inline-block;margin-right:10px;">
                <input type="checkbox" name="tags[]" value="<?= htmlspecialchars($tag['id']) ?>" <?= in_array($tag['id'], $selected_tag_ids) ? 'checked' : '' ?>>
                #<?= htmlspecialchars($tag['nama']) ?>
            </label>
        <?php endforeach; ?>
    </div><br>
    <label>Tingkat Kesulitan:</label><br>
    <select name="tingkat_kesulitan" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="Mudah" <?= ($recipe['tingkat_kesulitan'] == 'Mudah') ? 'selected' : '' ?>>Mudah</option>
        <option value="Sedang" <?= ($recipe['tingkat_kesulitan'] == 'Sedang') ? 'selected' : '' ?>>Sedang</option>
        <option value="Sulit" <?= ($recipe['tingkat_kesulitan'] == 'Sulit') ? 'selected' : '' ?>>Sulit</option>
    </select><br>
    <label>Foto (bisa lebih dari satu):</label><br>
    <input type="file" name="foto[]" accept="image/*" multiple><br>
    <button type="submit">Ajukan Perubahan</button>
</form>
<?php include 'footer.php'; ?>
</body>
</html>
