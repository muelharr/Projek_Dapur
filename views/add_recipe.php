<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/add.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php
require_once __DIR__ . '/../models/Tag.php';
$tags = Tag::getAll();
?>
<h2>Tambah Resep</h2>
<?php if (isset($add_success) && $add_success === true): ?>
    <div style="color:green;">Resep berhasil diajukan! Menunggu konfirmasi admin.</div>
<?php elseif (isset($add_success) && $add_success === false): ?>
    <div style="color:red;">Gagal menyimpan resep. Silakan coba lagi.</div>
<?php endif; ?>
<form method="post" action="index.php?action=add_recipe" enctype="multipart/form-data" style="max-height:480px; overflow-y:auto; padding-right:8px;">
    <label>Judul:</label><br>
    <input type="text" name="judul" required><br>
    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" required></textarea><br>
    <div style="display:flex; gap:10px; width:100%;">
        <div style="flex:1; min-width:0;">
            <label>Bahan:</label>
            <textarea name="bahan" required style="min-height:60px;"></textarea>
        </div>
        <div style="flex:1; min-width:0;">
            <label>Langkah:</label>
            <textarea name="langkah" required style="min-height:60px;"></textarea>
        </div>
    </div>
    <label>Kategori:</label><br>
    <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Makanan">Makanan</option>
        <option value="Minuman">Minuman</option>
        <option value="Cemilan">Cemilan</option>
        <option value="Kue">Kue</option>
    </select><br>
    <label>Tags (opsional):</label><br>
    <div class="tags-checkboxes">
        <?php foreach ($tags as $tag): ?>
            <label style="display:inline-block;margin-right:10px;">
                <input type="checkbox" name="tags[]" value="<?= htmlspecialchars($tag['id']) ?>">
                #<?= htmlspecialchars($tag['nama']) ?>
            </label>
        <?php endforeach; ?>
    </div><br>
    <label>Tingkat Kesulitan:</label><br>
    <select name="tingkat_kesulitan" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="Mudah">Mudah</option>
        <option value="Sedang">Sedang</option>
        <option value="Sulit">Sulit</option>
    </select><br>
    <label>Foto (bisa lebih dari satu):</label><br>
    <input type="file" name="foto[]" accept="image/*" multiple><br>
    <button type="submit">Ajukan</button>
    </form>
<?php include 'footer.php'; ?>
</body>
</html>
