<?php
require_once __DIR__ . '/../config/db.php';

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($nama, $email, $password, $role = 'user') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$nama, $email, $hash, $role]);
        } catch (PDOException $e) {
            // Jika email sudah terdaftar, kembalikan false
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
?>
