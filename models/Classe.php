<?php
require_once __DIR__ . '/Model.php';

class Classe extends Model {
    protected $table = 'classes';
    
    public function getAllWithDetails() {
        $sql = "SELECT c.*, o.nom as option_nom, o.id as option_id,
                       COUNT(i.id) as nombre_eleves
                FROM classes c
                JOIN options o ON c.option_id = o.id
                LEFT JOIN inscriptions i ON c.id = i.classe_id AND i.statut = 'actif'
                WHERE c.actif = 1
                GROUP BY c.id
                ORDER BY c.niveau, c.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getOptions() {
        $sql = "SELECT id, nom FROM options WHERE actif = 1 ORDER BY nom";
        return $this->db->query($sql)->fetchAll();
    }
}