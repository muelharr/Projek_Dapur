<?php
require_once __DIR__ . '/../config/db.php';

class Favorite {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function add($user_id, $recipe_id) {
        $stmt = $this->pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $recipe_id]);
    }

    public function remove($user_id, $recipe_id) {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        return $stmt->execute([$user_id, $recipe_id]);
    }

    public function isFavorite($user_id, $recipe_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
        return $stmt->fetch() ? true : false;
    }
    public function getByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM favorites WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
?>
