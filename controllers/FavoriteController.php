<?php
require_once __DIR__ . '/../models/Favorite.php';

class FavoriteController {
    private $favoriteModel;
    public function __construct($pdo) {
        $this->favoriteModel = new Favorite($pdo);
    }
    public function add($user_id, $recipe_id) {
        return $this->favoriteModel->add($user_id, $recipe_id);
    }
    public function remove($user_id, $recipe_id) {
        return $this->favoriteModel->remove($user_id, $recipe_id);
    }
    public function isFavorite($user_id, $recipe_id) {
        return $this->favoriteModel->isFavorite($user_id, $recipe_id);
    }
    public function getByUser($user_id) {
        return $this->favoriteModel->getByUser($user_id);
    }
}
?>
