<?php
require_once __DIR__ . '/../config/db.php';

class Tag {
    public static function getAll() {
        global $pdo;
        $sql = "SELECT * FROM tags ORDER BY nama ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function getByRecipe($recipe_id) {
        global $pdo;
        $sql = "SELECT t.* FROM tags t JOIN recipe_tags rt ON t.id = rt.tag_id WHERE rt.recipe_id = ? ORDER BY t.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$recipe_id]);
        return $stmt->fetchAll();
    }
}
