<?php
require_once __DIR__ . '/../models/Recipe.php';

class RecipeController {
    private $recipeModel;
    public function __construct($pdo) {
        $this->recipeModel = new Recipe($pdo);
    }
    public function index() {
        return $this->recipeModel->getAllApproved();
    }
    public function pending() {
        return $this->recipeModel->getAllPending();
    }
    public function approve($id) {
        return $this->recipeModel->approve($id);
    }
    public function reject($id) {
        return $this->recipeModel->reject($id);
    }
    public function getByUser($user_id) {
        return $this->recipeModel->getByUser($user_id);
    }
    public function detail($id) {
        return $this->recipeModel->getById($id);
    }
    public function create($data) {
        return $this->recipeModel->create($data);
    }
    public function updateRequest($id, $data) {
        return $this->recipeModel->updateRequest($id, $data);
    }
    public function applyEdit($id) {
        return $this->recipeModel->applyEdit($id);
    }
    public function rejectEdit($id) {
        return $this->recipeModel->rejectEdit($id);
    }
    public function getAll() {
        return $this->recipeModel->getAll();
    }
    public function delete($id) {
        return $this->recipeModel->delete($id);
    }
    // Tambahkan update, delete sesuai kebutuhan

    // Fitur pencarian resep dengan filter
    public function search($q = '', $kategori = '', $tingkat = '', $min_rating = '') {
        return $this->recipeModel->search($q, $kategori, $tingkat, $min_rating);
    }
}
?>
