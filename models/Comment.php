<?php
require_once __DIR__ . '/../config/db.php';

class Comment {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getByRecipe($recipe_id) {
        $stmt = $this->pdo->prepare("SELECT comments.*, users.nama FROM comments JOIN users ON comments.user_id = users.id WHERE comments.recipe_id = ? ORDER BY comments.created_at ASC");
        $stmt->execute([$recipe_id]);
        return $stmt->fetchAll();
    }
    public function add($recipe_id, $user_id, $komentar) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (recipe_id, user_id, komentar) VALUES (?, ?, ?)");
        return $stmt->execute([
            $recipe_id,
            $user_id,
            $komentar
        ]);
    }
    public function delete($comment_id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$comment_id]);
    }
}
?>
