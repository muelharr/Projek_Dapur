<?php
require_once __DIR__ . '/../models/Rating.php';

class RatingController {
    private $ratingModel;
    public function __construct($pdo) {
        $this->ratingModel = new Rating($pdo);
    }
    public function add($data) {
        return $this->ratingModel->add($data);
    }
    public function update($data) {
        return $this->ratingModel->update($data);
    }
    public function getAverage($recipe_id) {
        return $this->ratingModel->getAverage($recipe_id);
    }
    public function getUserRating($recipe_id, $user_id) {
        return $this->ratingModel->getUserRating($recipe_id, $user_id);
    }
}
?>
