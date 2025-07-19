<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dapur Kreatif – Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="views/css/login.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-yellow-50 flex flex-col min-h-screen">
    <?php include 'header.php'; ?>

  <!-- Wrapper login form -->
  <div class="flex-grow flex items-center justify-center">
    <div class="login-container bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
      <h2 class="text-3xl font-bold text-center text-orange-500 mb-6">Login</h2>
      <?php if (isset($login_error)): ?>
        <div class="bg-red-100 text-red-700 border border-red-200 p-4 rounded mb-4">
          <?= htmlspecialchars($login_error) ?>
        </div>
      <?php endif; ?>

      <form class="flex flex-col space-y-4" method="post" action="index.php?action=login">
        <div>
          <label for="email" class="block mb-1 font-medium text-gray-700">Email:</label>
          <input id="email" type="email" name="email" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md
                        focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition">
        </div>
        <div>
          <label for="password" class="block mb-1 font-medium text-gray-700">Password:</label>
          <input id="password" type="password" name="password" required
                 class="w-full px-4 py-2 border border-gray-300 rounded-md
                        focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition">
        </div>
        <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-orange-400 to-yellow-400
                       text-white font-semibold rounded-md shadow
                       hover:from-orange-500 hover:to-yellow-500 transition">
          Login
        </button>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>
