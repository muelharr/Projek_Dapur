<?php
// controllers/RecipePhotoController.php
require_once __DIR__ . '/../models/RecipePhoto.php';
class RecipePhotoController {
    private $photoModel;
    public function __construct($pdo) {
        $this->photoModel = new RecipePhoto($pdo);
    }
    public function addPhotos($recipe_id, $file_paths) {
        return $this->photoModel->addPhotos($recipe_id, $file_paths);
    }
    public function getByRecipe($recipe_id) {
        return $this->photoModel->getByRecipe($recipe_id);
    }
}
