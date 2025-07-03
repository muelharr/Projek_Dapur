<?php include 'header.php'; ?>
<h2>Detail Resep</h2>
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/RecipeController.php';
$recipeController = new RecipeController($pdo);
// Hanya tampilkan detail jika:
// - resep approved
// - user adalah pemilik resep
// - admin (bisa lihat semua, termasuk pending/edit_pending)
$id = $_GET['id'] ?? null;
if ($id) {
    $recipe = $recipeController->detail($id);
    $can_view = false;
    if ($recipe) {
        if ($recipe['status'] === 'approved') {
            $can_view = true;
        } elseif (isset($_SESSION['user_id']) && $recipe['user_id'] == $_SESSION['user_id']) {
            $can_view = true;
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $can_view = true;
        }
    }
    if ($can_view && $recipe): ?>
        <div style="border:1px solid #ccc; padding:15px; max-width:600px;">
            <h3><?= htmlspecialchars($recipe['judul']) ?></h3>
            <?php if (!empty($recipe['foto_url'])): ?>
                <img src="<?= htmlspecialchars($recipe['foto_url']) ?>" alt="Foto Resep" style="max-width:100%;height:auto;">
            <?php endif; ?>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($recipe['kategori']) ?></p>
            <p><strong>Tingkat Kesulitan:</strong> <?= htmlspecialchars($recipe['tingkat_kesulitan']) ?></p>
            <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($recipe['deskripsi'])) ?></p>
            <p><strong>Bahan:</strong><br><?= nl2br(htmlspecialchars($recipe['bahan'])) ?></p>
            <p><strong>Langkah:</strong><br><?= nl2br(htmlspecialchars($recipe['langkah'])) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($recipe['status'] ?? '-') ?></p>

            <!-- RATING DAN FAVORIT -->
            <?php
            require_once __DIR__ . '/../controllers/RatingController.php';
            require_once __DIR__ . '/../controllers/FavoriteController.php';
            $ratingController = new RatingController($pdo);
            $favoriteController = new FavoriteController($pdo);
            $avg = $ratingController->getAverage($id);
            $user_rating = null;
            $is_fav = false;
            if (isset($_SESSION['user_id'])) {
                $user_rating = $ratingController->getUserRating($id, $_SESSION['user_id']);
                $is_fav = $favoriteController->isFavorite($_SESSION['user_id'], $id);
            }
            ?>
            <div style="margin:10px 0;">
                <b>Rating:</b>
                <?php if ($avg && $avg['avg_rating']): ?>
                    <?= number_format($avg['avg_rating'],1) ?> / 5 (<?= $avg['jumlah'] ?> rating)
                <?php else: ?>
                    Belum ada rating
                <?php endif; ?>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="index.php?action=add_rating" style="display:inline-block; margin-right:15px;">
                    <input type="hidden" name="recipe_id" value="<?= $id ?>">
                    <label for="nilai"><b>Beri Rating:</b></label>
                    <span class="star-rating">
                        <?php for ($i=5; $i>=1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="nilai" value="<?= $i ?>" style="display:none;" <?= ($user_rating && $user_rating['nilai']==$i)?'checked':'' ?> />
                            <label for="star<?= $i ?>" style="font-size:2em; color:<?= ($user_rating && $user_rating['nilai']>=$i)?'#ffb700':'#ccc' ?>; cursor:pointer;">&#9733;</label>
                        <?php endfor; ?>
                    </span>
                    <button type="submit">Kirim</button>
                </form>
                <style>
                .star-rating {
                    direction: rtl;
                    unicode-bidi: bidi-override;
                }
                .star-rating label {
                    display: inline-block;
                }
                .star-rating input[type="radio"]:checked ~ label,
                .star-rating label:hover,
                .star-rating label:hover ~ label {
                    color: #ffb700 !important;
                }
                </style>
                <form method="get" action="index.php" style="display:inline-block;">
                    <input type="hidden" name="action" value="toggle_favorite">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" style="background:<?= $is_fav?'#ffb700':'#eee' ?>;color:#222;padding:5px 12px;border-radius:5px;border:1px solid #ccc;cursor:pointer;">
                        <?= $is_fav ? '★ Disimpan' : '☆ Simpan Resep' ?>
                    </button>
                </form>
            <?php else: ?>
                <div style="color:gray;">Login untuk memberi rating & menyimpan resep.</div>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && $recipe['status'] === 'edit_pending' && $recipe['edit_data']): ?>
                <hr>
                <h4>Data Perubahan (Menunggu Konfirmasi)</h4>
                <?php $edit = json_decode($recipe['edit_data'], true); if ($edit): ?>
                    <ul>
                        <li><b>Judul:</b> <?= htmlspecialchars($edit['judul']) ?></li>
                        <li><b>Deskripsi:</b> <?= nl2br(htmlspecialchars($edit['deskripsi'])) ?></li>
                        <li><b>Bahan:</b> <?= nl2br(htmlspecialchars($edit['bahan'])) ?></li>
                        <li><b>Langkah:</b> <?= nl2br(htmlspecialchars($edit['langkah'])) ?></li>
                        <li><b>Kategori:</b> <?= htmlspecialchars($edit['kategori']) ?></li>
                        <li><b>Tingkat Kesulitan:</b> <?= htmlspecialchars($edit['tingkat_kesulitan']) ?></li>
                        <li><b>Foto:</b> <?php if (!empty($edit['foto_url'])): ?><img src="<?= htmlspecialchars($edit['foto_url']) ?>" alt="Foto Edit" style="max-width:100px;vertical-align:middle;"><?php endif; ?></li>
                    </ul>
                    <a href="index.php?action=dashboard_admin&apply_edit=<?= $recipe['id'] ?>" style="color:green;">Setujui Edit</a> |
                    <a href="index.php?action=dashboard_admin&reject_edit=<?= $recipe['id'] ?>" style="color:red;">Tolak Edit</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Komentar -->
        <div style="margin-top:30px; max-width:600px;">
        <h3>Komentar</h3>
        <?php
        require_once __DIR__ . '/../controllers/CommentController.php';
        $commentController = new CommentController($pdo);
        // Proses tambah komentar
        if (isset($_POST['komentar']) && isset($_SESSION['user_id']) && trim($_POST['komentar']) !== '') {
            $commentController->add($id, $_SESSION['user_id'], $_POST['komentar']);
            // Refresh agar tidak resubmit
            echo "<script>location.href='index.php?action=recipe_detail&id=" . urlencode($id) . "';</script>";
            exit;
        }
        // Hapus komentar jika admin dan ada request delete
        if (isset($_GET['delete_comment']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $commentController->delete($_GET['delete_comment']);
            echo "<script>location.href='index.php?action=recipe_detail&id=" . urlencode($id) . "';</script>";
            exit;
        }
        $comments = $commentController->getByRecipe($id);
        ?>
        <?php if (isset($_SESSION['user_id'])): ?>
        <form method="post" style="margin-bottom:20px;">
            <textarea name="komentar" rows="2" style="width:100%;" placeholder="Tulis komentar..." required></textarea><br>
            <button type="submit">Kirim</button>
        </form>
        <?php else: ?>
        <div style="color:gray;">Login untuk menulis komentar.</div>
        <?php endif; ?>
        <?php if ($comments && count($comments) > 0): ?>
            <?php foreach ($comments as $c): ?>
                <div style="border-bottom:1px solid #eee; padding:8px 0;">
                    <b><?= htmlspecialchars($c['nama']) ?></b> <span style="color:#888; font-size:12px;">(<?= date('d-m-Y H:i', strtotime($c['created_at'])) ?>)</span>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="index.php?action=recipe_detail&id=<?= urlencode($id) ?>&delete_comment=<?= $c['id'] ?>" onclick="return confirm('Yakin hapus komentar ini?');" style="color:red; float:right; font-size:12px;">Hapus</a>
                    <?php endif; ?><br>
                    <?= nl2br(htmlspecialchars($c['komentar'])) ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:gray;">Belum ada komentar.</div>
        <?php endif; ?>
        </div>
    <?php else:
        echo '<p>Resep tidak ditemukan atau belum disetujui.</p>';
    endif;
} else {
    echo '<p>ID resep tidak ditemukan.</p>';
}
?>
<?php include 'footer.php'; ?>
