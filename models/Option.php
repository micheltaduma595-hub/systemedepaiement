<?php
require_once __DIR__ . '/Model.php';

class Option extends Model {
    protected $table = 'options';
    
    public function getAllActives() {
        return $this->findAll(['actif' => 1], 'nom ASC');
    }
    
    public function getWithClasses() {
        $sql = "SELECT o.*, COUNT(c.id) as nombre_classes
                FROM options o
                LEFT JOIN classes c ON o.id = c.option_id AND c.actif = 1
                WHERE o.actif = 1
                GROUP BY o.id
                ORDER BY o.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}