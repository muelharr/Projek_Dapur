<!DOCTYPE html>
<html>
<head>
    <title>Dapur Kreatif</title>
    <link rel="stylesheet" type="text/css" href="views/css/dashboard.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50">
        <?php include 'header.php'; ?>
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Upload Button -->
        <div class="mb-8">
            <a href="index.php?action=add_recipe" 
               class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center space-x-2 shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Upload Resep</span>
            </a>
        </div>

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
        if ($pending_count > 0): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <i class="fas fa-clock text-amber-500 mr-3"></i>
                    <p class="text-amber-800">
                        Anda memiliki <span class="font-bold"><?= $pending_count ?></span> resep yang masih 
                        <span class="font-bold">menunggu konfirmasi admin</span>.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Resep Terbaru Section -->
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mr-4">Resep Terbaru</h2>
                <div class="h-1 bg-gradient-to-r from-orange-400 to-yellow-400 rounded flex-grow max-w-32"></div>
            </div>

            <?php
            $all_approved = $recipeController->index();
            $favorites = $favoriteController->getByUser($user_id);
            $favorite_ids = array_map(function($fav){ return $fav['recipe_id']; }, $favorites);
            
            if ($all_approved && count($all_approved) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($all_approved as $recipe): ?>
                        <div class="recipe-card glass-effect rounded-xl p-6 shadow-lg">
                            <!-- Recipe Image Placeholder -->
                            <div class="w-full h-48 bg-gradient-to-br to-yellow-200 rounded-lg mb-4 flex items-center justify-center">
                            <img src="<?= htmlspecialchars($recipe['foto_url'] ?? 'views/images/placeholder.png') ?>" 
                                 alt="<?= htmlspecialchars($recipe['judul']) ?>" 
                                 class="w-full h-full object-cover rounded-lg">    
                            </div>
                            
                            <!-- Recipe Info -->
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    <?= htmlspecialchars($recipe['judul']) ?>
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-user-chef mr-1"></i>
                                    Oleh: <?= htmlspecialchars($recipe['user_id'] == $_SESSION['user_id'] ? 'Saya' : ($recipe['nama'] ?? ('User #' . $recipe['user_id'])) ) ?>
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between">
                                <a href="index.php?action=recipe_detail&id=<?= $recipe['id'] ?>" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat Detail
                                </a>
                                
                                <div class="flex items-center space-x-2">
                                    <?php if ($recipe['user_id'] == $_SESSION['user_id']): ?>
                                        <a href="index.php?action=edit_recipe&id=<?= urlencode($recipe['id']) ?>" 
                                           class="text-orange-500 hover:text-orange-600 p-2 rounded-lg hover:bg-orange-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array($recipe['id'], $favorite_ids)): ?>
                                        <span class="favorite-star text-xl">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada resep yang disetujui.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Resep Favorit Section -->
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-800 mr-4">Resep Favorit Saya</h2>
                <div class="h-1 bg-gradient-to-r from-yellow-400 to-orange-400 rounded flex-grow max-w-32"></div>
                <i class="fas fa-star favorite-star text-2xl ml-4"></i>
            </div>

            <?php if ($favorites && count($favorites) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($favorites as $fav):
                        $fav_recipe = $recipeController->detail($fav['recipe_id']);
                        if ($fav_recipe && $fav_recipe['status'] === 'approved'): ?>
                            <div class="recipe-card bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-xl p-6 shadow-lg">
                                <!-- Favorite Badge -->
                                <div class="flex justify-between items-start mb-4">
                                    <span class="bg-yellow-400 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">
                                        <i class="fas fa-star mr-1"></i>
                                        Favorit
                                    </span>
                                    <i class="fas fa-heart text-red-400 text-xl"></i>
                                </div>
                                
                                <!-- Recipe Image Placeholder -->
                                <div class="w-full h-40 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-lg mb-4 flex items-center justify-center">
                                <img src="<?= htmlspecialchars($fav_recipe['foto_url'] ?? 'views/images/placeholder.png') ?>" 
                                     alt="<?= htmlspecialchars($fav_recipe['judul']) ?>" 
                                     class="w-full h-full object-cover rounded-lg">
                                </div>
                                
                                <!-- Recipe Info -->
                                <h3 class="text-lg font-bold text-gray-800 mb-3">
                                    <?= htmlspecialchars($fav_recipe['judul']) ?>
                                </h3>
                                
                                <!-- Action -->
                                <a href="index.php?action=recipe_detail&id=<?= $fav_recipe['id'] ?>" 
                                   class="bg-gradient-to-r from-yellow-400 to-orange-400 hover:from-orange-400 hover:to-yellow-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 inline-flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </a>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl border-2 border-dashed border-yellow-200">
                    <i class="fas fa-star text-6xl text-yellow-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Belum ada resep favorit.</p>
                    <p class="text-gray-400 text-sm">Mulai menandai resep sebagai favorit untuk melihatnya di sini!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
            <?php include 'footer.php'; ?>

    <!-- Floating Action Button untuk Mobile -->
    <div class="fixed bottom-6 right-6 md:hidden">
        <a href="index.php?action=add_recipe" 
           class="btn-primary w-14 h-14 rounded-full flex items-center justify-center text-white shadow-2xl">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>
</body>
</html>