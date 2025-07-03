<?php include 'header.php'; ?>
<h2>Login</h2>
<?php if (isset($login_error)): ?>
    <div style="color:red;"> <?= $login_error ?> </div>
<?php endif; ?>
<form method="post" action="index.php?action=login">
    <label>Email:</label><br>
    <input type="email" name="email" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<?php include 'footer.php'; ?>
