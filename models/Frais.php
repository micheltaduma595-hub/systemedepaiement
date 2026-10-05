<?php
require_once __DIR__ . '/Model.php';

class Frais extends Model {
    protected $table = 'frais';
    
    public function getAllWithDetails() {
        $sql = "SELECT f.*, 
                       tf.libelle as type_frais_libelle, tf.id as type_frais_id,
                       c.nom as classe_nom, c.niveau,
                       s.nom as semestre_nom, s.id as semestre_id,
                       cp.libelle as echeance_libelle, cp.date_fin as echeance_fin,
                       aa.libelle as annee_academique
                FROM frais f
                JOIN types_frais tf ON f.type_frais_id = tf.id
                JOIN classes c ON f.classe_id = c.id
                JOIN semestres s ON f.semestre_id = s.id
                LEFT JOIN calendrier_paiement cp ON f.calendrier_id = cp.id
                JOIN annees_academiques aa ON s.annee_id = aa.id
                WHERE f.statut = 'actif'
                ORDER BY c.nom, f.date_limite";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getFraisByClasse($classeId) {
        $sql = "SELECT f.*, 
                       tf.libelle as type_frais_libelle,
                       s.nom as semestre_nom,
                       cp.libelle as echeance_libelle, cp.date_fin as echeance_fin
                FROM frais f
                JOIN types_frais tf ON f.type_frais_id = tf.id
                JOIN semestres s ON f.semestre_id = s.id
                LEFT JOIN calendrier_paiement cp ON f.calendrier_id = cp.id
                WHERE f.classe_id = :classe_id AND f.statut = 'actif'
                ORDER BY f.date_limite";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['classe_id' => $classeId]);
        return $stmt->fetchAll();
    }
    
    public function getTotalFraisByClasse($classeId) {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total
                FROM frais
                WHERE classe_id = :classe_id AND statut = 'actif'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['classe_id' => $classeId]);
        return $stmt->fetch()['total'];
    }
    
    public function getTypesFrais() {
        return $this->db->query("SELECT * FROM types_frais WHERE actif = 1 ORDER BY libelle")->fetchAll();
    }
    
    public function getSemestres() {
        $sql = "SELECT s.*, aa.libelle as annee 
                FROM semestres s 
                JOIN annees_academiques aa ON s.annee_id = aa.id 
                WHERE s.actif = 1 
                ORDER BY s.date_debut";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getClasses() {
        return $this->db->query("SELECT c.*, o.nom as option_nom FROM classes c JOIN options o ON c.option_id = o.id WHERE c.actif = 1 ORDER BY c.nom")->fetchAll();
    }
    
    public function getCalendriers() {
        $sql = "SELECT cp.*, aa.libelle as annee 
                FROM calendrier_paiement cp 
                JOIN annees_academiques aa ON cp.annee_id = aa.id 
                ORDER BY cp.date_fin DESC";
        return $this->db->query($sql)->fetchAll();
    }
}