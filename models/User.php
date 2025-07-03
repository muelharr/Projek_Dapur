<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public function getAllAdmins() {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE role = 'admin'");
        return $stmt->fetchAll();
    }
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE role = 'user'");
        return $stmt->fetchAll();
    }
}
?>
