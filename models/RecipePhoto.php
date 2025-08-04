<?php
// models/RecipePhoto.php
require_once __DIR__ . '/../config/db.php';
class RecipePhoto {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function addPhotos($recipe_id, $file_paths) {
        $stmt = $this->pdo->prepare("INSERT INTO recipe_photos (recipe_id, file_path) VALUES (?, ?)");
        foreach ($file_paths as $path) {
            $stmt->execute([$recipe_id, $path]);
        }
    }
    public function getByRecipe($recipe_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM recipe_photos WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        return $stmt->fetchAll();
    }
}
