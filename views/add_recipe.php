<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/add.css">
</head>
<body>
<?php include 'header.php'; ?>
<h2>Tambah Resep</h2>
<?php if (isset($add_success) && $add_success === true): ?>
    <div style="color:green;">Resep berhasil diajukan! Menunggu konfirmasi admin.</div>
<?php elseif (isset($add_success) && $add_success === false): ?>
    <div style="color:red;">Gagal menyimpan resep. Silakan coba lagi.</div>
<?php endif; ?>
<form method="post" action="index.php?action=add_recipe" enctype="multipart/form-data">
    <label>Judul:</label><br>
    <input type="text" name="judul" required><br>
    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" required></textarea><br>
    <label>Bahan:</label><br>
    <textarea name="bahan" required></textarea><br>
    <label>Langkah:</label><br>
    <textarea name="langkah" required></textarea><br>
    <label>Kategori:</label><br>
    <select name="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="Makanan">Makanan</option>
        <option value="Minuman">Minuman</option>
        <option value="Cemilan">Cemilan</option>
        <option value="Kue">Kue</option>
    </select><br>
    <label>Tingkat Kesulitan:</label><br>
    <select name="tingkat_kesulitan" required>
        <option value="">-- Pilih Tingkat --</option>
        <option value="Mudah">Mudah</option>
        <option value="Sedang">Sedang</option>
        <option value="Sulit">Sulit</option>
    </select><br>
    <label>Foto:</label><br>
    <input type="file" name="foto" accept="image/*"><br>
    <button type="submit">Ajukan</button>
</form>
<?php include 'footer.php'; ?>
</body>
</html>
