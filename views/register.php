<?php include 'header.php'; ?>
<h2>Register</h2>
<?php if (isset($register_success) && $register_success === true): ?>
    <div style="color:green;">Registrasi berhasil! Silakan <a href="index.php?action=login">login</a>.</div>
<?php elseif (isset($register_success) && $register_success === false): ?>
    <div style="color:red;">Registrasi gagal! Email mungkin sudah terdaftar.</div>
<?php endif; ?>
<form method="post" action="index.php?action=register">
    <label>Nama:</label><br>
    <input type="text" name="nama" required><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
<?php include 'footer.php'; ?>
