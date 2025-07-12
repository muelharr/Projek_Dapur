<?php
// FINAL CLEANUP: Only one PHP block, no duplicate code, no extra PHP tags
session_start();
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'search':
        include __DIR__ . '/views/search.php';
        break;
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController($pdo);
        $login_error = null;
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                header('Location: index.php?action=dashboard_admin');
            } else {
                header('Location: index.php?action=dashboard');
            }
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($auth->login($email, $password)) {
                $stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                $_SESSION['role'] = $user['role'] ?? 'user';
                if ($_SESSION['role'] === 'admin') {
                    header('Location: index.php?action=dashboard_admin');
                } else {
                    header('Location: index.php?action=dashboard');
                }
                exit;
            } else {
                $login_error = 'Email atau password salah!';
            }
        }
        include __DIR__ . '/views/login.php';
        break;
    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController($pdo);
        $register_success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($auth->register($nama, $email, $password)) {
                $register_success = true;
            } else {
                $register_success = false;
            }
        }
        include __DIR__ . '/views/register.php';
        break;
    case 'dashboard':
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/views/dashboard.php';
        break;
    case 'dashboard_admin':
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/views/dashboard_admin.php';
        break;
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController($pdo);
        $auth->logout();
        header('Location: index.php?action=login');
        exit;
    case 'add_recipe':
        require_once __DIR__ . '/controllers/RecipeController.php';
        $recipeController = new RecipeController($pdo);
        $add_success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul = $_POST['judul'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $bahan = $_POST['bahan'] ?? '';
            $langkah = $_POST['langkah'] ?? '';
            $kategori = $_POST['kategori'] ?? '';
            $tingkat_kesulitan = $_POST['tingkat_kesulitan'] ?? '';
            $foto_url = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $newname = 'resep_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $target = __DIR__ . '/uploads/' . $newname;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                    $foto_url = 'uploads/' . $newname;
                }
            }
            $data = [
                'user_id' => $_SESSION['user_id'],
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'bahan' => $bahan,
                'langkah' => $langkah,
                'foto_url' => $foto_url,
                'kategori' => $kategori,
                'tingkat_kesulitan' => $tingkat_kesulitan
            ];
            if ($recipeController->create($data)) {
                echo "<script>location.href='index.php?action=dashboard';</script>";
                exit;
            } else {
                $add_success = false;
            }
        }
        include __DIR__ . '/views/add_recipe.php';
        break;
    case 'edit_recipe':
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/controllers/RecipeController.php';
        $recipeController = new RecipeController($pdo);
        $edit_success = null;
        $edit_error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recipe_id = $_POST['id'] ?? null;
            $judul = $_POST['judul'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $bahan = $_POST['bahan'] ?? '';
            $langkah = $_POST['langkah'] ?? '';
            $kategori = $_POST['kategori'] ?? '';
            $tingkat_kesulitan = $_POST['tingkat_kesulitan'] ?? '';
            $foto_url = $_POST['foto_url'] ?? '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $newname = 'resep_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $target = __DIR__ . '/uploads/' . $newname;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                    $foto_url = 'uploads/' . $newname;
                }
            }
            $edit_data = [
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'bahan' => $bahan,
                'langkah' => $langkah,
                'foto_url' => $foto_url,
                'kategori' => $kategori,
                'tingkat_kesulitan' => $tingkat_kesulitan
            ];
            if ($recipeController->updateRequest($recipe_id, $edit_data, $_SESSION['user_id'])) {
                $edit_success = true;
            } else {
                $edit_error = 'Gagal mengajukan edit resep.';
            }
        }
        include __DIR__ . '/views/edit_recipe.php';
        break;
    case 'add_rating':
        require_once __DIR__ . '/controllers/RatingController.php';
        $ratingController = new RatingController($pdo);
        if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $recipe_id = $_POST['recipe_id'] ?? null;
            $nilai = $_POST['nilai'] ?? null;
            if ($recipe_id && $nilai) {
                $existing = $ratingController->getUserRating($recipe_id, $_SESSION['user_id']);
                $data = [
                    'recipe_id' => $recipe_id,
                    'user_id' => $_SESSION['user_id'],
                    'nilai' => $nilai
                ];
                if ($existing) {
                    $ratingController->update($data);
                } else {
                    $ratingController->add($data);
                }
            }
            echo "<script>location.href='index.php?action=recipe_detail&id=" . urlencode($recipe_id) . "';</script>";
            exit;
        }
        break;
    case 'toggle_favorite':
        require_once __DIR__ . '/controllers/FavoriteController.php';
        $favoriteController = new FavoriteController($pdo);
        if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
            $recipe_id = $_GET['id'];
            $is_fav = $favoriteController->isFavorite($_SESSION['user_id'], $recipe_id);
            if ($is_fav) {
                $favoriteController->remove($_SESSION['user_id'], $recipe_id);
            } else {
                $favoriteController->add($_SESSION['user_id'], $recipe_id);
            }
            echo "<script>location.href='index.php?action=recipe_detail&id=" . urlencode($recipe_id) . "';</script>";
            exit;
        }
        break;
    case 'recipe_detail':
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['visitor_viewed'])) {
            $_SESSION['visitor_viewed'] = true;
            include __DIR__ . '/views/recipe_detail.php';
        } elseif (!isset($_SESSION['user_id']) && isset($_SESSION['visitor_viewed'])) {
            header('Location: index.php?action=register&info=akses_terbatas');
            exit;
        } else {
            include __DIR__ . '/views/recipe_detail.php';
        }
        break;
    default:
        include __DIR__ . '/views/home.php';
        break;
}


// END OF ROUTING, NO DUPLICATE CODE BELOW THIS LINE
//