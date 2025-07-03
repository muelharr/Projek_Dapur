<?php
require_once __DIR__ . '/../models/Comment.php';

class CommentController {
    private $commentModel;
    public function __construct($pdo) {
        $this->commentModel = new Comment($pdo);
    }
    public function add($recipe_id, $user_id, $komentar) {
        return $this->commentModel->add($recipe_id, $user_id, $komentar);
    }
    public function getByRecipe($recipe_id) {
        return $this->commentModel->getByRecipe($recipe_id);
    }
    public function delete($comment_id) {
        return $this->commentModel->delete($comment_id);
    }
}
?>
