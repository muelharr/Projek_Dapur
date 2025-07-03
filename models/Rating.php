<?php
require_once __DIR__ . '/../config/db.php';

class Rating {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO ratings (recipe_id, user_id, nilai) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['recipe_id'],
            $data['user_id'],
            $data['nilai']
        ]);
    }

    public function update($data) {
        $stmt = $this->pdo->prepare("UPDATE ratings SET nilai = ? WHERE recipe_id = ? AND user_id = ?");
        return $stmt->execute([
            $data['nilai'],
            $data['recipe_id'],
            $data['user_id']
        ]);
    }

    public function getUserRating($recipe_id, $user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE recipe_id = ? AND user_id = ?");
        $stmt->execute([$recipe_id, $user_id]);
        return $stmt->fetch();
    }
    public function getAverage($recipe_id) {
        $stmt = $this->pdo->prepare("SELECT AVG(nilai) as avg_rating, COUNT(*) as jumlah FROM ratings WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        return $stmt->fetch();
    }
}
?>
