<?php
require_once __DIR__ . '/../config/db.php';

class Recipe {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllApproved() {
        // Ambil juga nama user
        $stmt = $this->pdo->query("SELECT recipes.*, users.nama FROM recipes JOIN users ON recipes.user_id = users.id WHERE recipes.status = 'approved' ORDER BY recipes.created_at DESC");
        return $stmt->fetchAll();
    }
    public function getAllPending() {
        $stmt = $this->pdo->query("SELECT recipes.*, users.nama FROM recipes JOIN users ON recipes.user_id = users.id WHERE status IN ('pending','edit_pending') ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    public function updateRequest($id, $data) {
        // Update langsung field utama, set status ke pending, dan edit_data NULL
        $sql = "UPDATE recipes SET judul=?, deskripsi=?, bahan=?, langkah=?, foto_url=?, kategori=?, tingkat_kesulitan=?, status='pending', edit_data=NULL WHERE id=?";
        $params = [
            $data['judul'],
            $data['deskripsi'],
            $data['bahan'],
            $data['langkah'],
            $data['foto_url'],
            $data['kategori'],
            $data['tingkat_kesulitan'],
            $id
        ];
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public function applyEdit($id) {
        // Ambil data edit_data, update field utama, set status approved, kosongkan edit_data
        $stmt = $this->pdo->prepare("SELECT edit_data FROM recipes WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['edit_data']) {
            $edit = json_decode($row['edit_data'], true);
            $sql = "UPDATE recipes SET judul=?, deskripsi=?, bahan=?, langkah=?, foto_url=?, kategori=?, tingkat_kesulitan=?, status='approved', edit_data=NULL WHERE id=?";
            $params = [
                $edit['judul'],
                $edit['deskripsi'],
                $edit['bahan'],
                $edit['langkah'],
                $edit['foto_url'],
                $edit['kategori'],
                $edit['tingkat_kesulitan'],
                $id
            ];
            $stmt2 = $this->pdo->prepare($sql);
            return $stmt2->execute($params);
        }
        return false;
    }
    public function rejectEdit($id) {
        $stmt = $this->pdo->prepare("UPDATE recipes SET status='approved', edit_data=NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function approve($id) {
        $stmt = $this->pdo->prepare("UPDATE recipes SET status = 'approved' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function reject($id) {
        // Hapus resep dari database jika ditolak admin
        $stmt = $this->pdo->prepare("DELETE FROM recipes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function getByUser($user_id) {
        // Ambil semua resep user, urutkan, status apapun
        $stmt = $this->pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT recipes.*, users.nama FROM recipes JOIN users ON recipes.user_id = users.id ORDER BY recipes.created_at DESC");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM recipes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM recipes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO recipes (user_id, judul, deskripsi, bahan, langkah, foto_url, kategori, tingkat_kesulitan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['user_id'],
            $data['judul'],
            $data['deskripsi'],
            $data['bahan'],
            $data['langkah'],
            $data['foto_url'],
            $data['kategori'],
            $data['tingkat_kesulitan']
        ]);
    }

    // Fungsi pencarian dengan filter
    public function search($q = '', $kategori = '', $tingkat = '', $min_rating = '') {
        $sql = "SELECT * FROM recipes WHERE status='approved'";
        $params = [];
        if ($q) {
            $sql .= " AND (judul LIKE ? OR deskripsi LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($kategori) {
            $sql .= " AND kategori = ?";
            $params[] = $kategori;
        }
        if ($tingkat) {
            $sql .= " AND tingkat_kesulitan = ?";
            $params[] = $tingkat;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        // Filter rating jika ada
        if ($min_rating) {
            require_once __DIR__ . '/Rating.php';
            $ratingModel = new Rating($this->pdo);
            $filtered = [];
            foreach ($results as $r) {
                $avg = $ratingModel->getAverage($r['id']);
                if ($avg && $avg['avg_rating'] && $avg['avg_rating'] >= $min_rating) {
                    $filtered[] = $r;
                }
            }
            return $filtered;
        }
        return $results;
    }
}
?>
