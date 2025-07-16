<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<h2>Edit Resep</h2>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
$recipeController = new RecipeController($pdo);
$id = $_GET['id'] ?? null;
$edit_success = null;
if (!$id) {
    echo '<p>ID resep tidak ditemukan.</p>';
    include 'footer.php';
    exit;
}
// Cegah edit jika status belum approved
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $bahan = $_POST['bahan'] ?? '';
    $langkah = $_POST['langkah'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $tingkat_kesulitan = $_POST['tingkat_kesulitan'] ?? '';
    $foto_url = $recipe['foto_url'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $newname = 'resep_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = __DIR__ . '/../uploads/' . $newname;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto_url = 'uploads/' . $newname;
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
        // Redirect ke dashboard setelah pengajuan edit
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
<form method="post" action="index.php?action=edit_recipe&amp;id=<?= urlencode($id) ?>" enctype="multipart/form-data">
    <label>Judul:</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($recipe['judul']) ?>" required><br>
    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" required><?= htmlspecialchars($recipe['deskripsi']) ?></textarea><br>
    <label>Bahan:</label><br>
    <textarea name="bahan" required><?= htmlspecialchars($recipe['bahan']) ?></textarea><br>
    <label>Langkah:</label><br>
    <textarea name="langkah" required><?= htmlspecialchars($recipe['langkah']) ?></textarea><br>
    <label>Kategori:</label><br>
    <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Makanan" <?= ($recipe['kategori'] == 'Makanan') ? 'selected' : '' ?>>Makanan</option>
        <option value="Minuman" <?= ($recipe['kategori'] == 'Minuman') ? 'selected' : '' ?>>Minuman</option>
        <option value="Cemilan" <?= ($recipe['kategori'] == 'Cemilan') ? 'selected' : '' ?>>Cemilan</option>
        <option value="Kue" <?= ($recipe['kategori'] == 'Kue') ? 'selected' : '' ?>>Kue</option>
    </select><br>
    <label>Tingkat Kesulitan:</label><br>
    <select name="tingkat_kesulitan" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="Mudah" <?= ($recipe['tingkat_kesulitan'] == 'Mudah') ? 'selected' : '' ?>>Mudah</option>
        <option value="Sedang" <?= ($recipe['tingkat_kesulitan'] == 'Sedang') ? 'selected' : '' ?>>Sedang</option>
        <option value="Sulit" <?= ($recipe['tingkat_kesulitan'] == 'Sulit') ? 'selected' : '' ?>>Sulit</option>
    </select><br>
    <label>Foto:</label><br>
    <input type="file" name="foto" accept="image/*"><br>
    <button type="submit">Ajukan Perubahan</button>
</form>
<?php include 'footer.php'; ?>
</body>
</html>
