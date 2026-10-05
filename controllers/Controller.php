<?php
class Controller {
    protected $db;
    
    public function __construct() {
        // Initialiser la connexion à la base de données si nécessaire
        if (class_exists('Database')) {
            $this->db = Database::getInstance()->getConnection();
        }
    }
    
    protected function render($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vue non trouvée: " . $view);
        }
    }
    
    protected function redirect($url) {
        header("Location: " . SITE_URL . "/" . $url);
        exit();
    }
    
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            header("Location: " . SITE_URL . "/auth/login.php");
            exit();
        }
    }
}