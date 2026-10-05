<?php
require_once __DIR__ . '/Model.php';

class Eleve extends Model {
    protected $table = 'eleves';
    
    public function getAllWithInscription() {
        $sql = "SELECT e.*, 
                       c.nom as classe_nom, 
                       c.niveau, 
                       o.nom as option_nom,
                       i.id as inscription_id, 
                       i.statut as statut_inscription,
                       aa.libelle as annee_academique
                FROM eleves e
                LEFT JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'actif'
                LEFT JOIN classes c ON i.classe_id = c.id
                LEFT JOIN options o ON c.option_id = o.id
                LEFT JOIN annees_academiques aa ON i.annee_id = aa.id AND aa.actif = 1
                WHERE e.actif = 1
                ORDER BY e.nom, e.prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function search($term) {
        $sql = "SELECT * FROM eleves 
                WHERE (nom LIKE :term1 OR prenom LIKE :term2 OR matricule LIKE :term3)
                AND actif = 1
                ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $term = "%$term%";
        $stmt->execute(['term1' => $term, 'term2' => $term, 'term3' => $term]);
        return $stmt->fetchAll();
    }
    
    public function getEleveDetail($id) {
        $sql = "SELECT e.*, 
                       i.id as inscription_id, 
                       i.date_inscription, 
                       i.statut as statut_inscription,
                       c.nom as classe_nom, 
                       c.niveau, 
                       c.id as classe_id,
                       o.nom as option_nom, 
                       o.id as option_id,
                       aa.libelle as annee_academique, 
                       aa.id as annee_id
                FROM eleves e
                LEFT JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'actif'
                LEFT JOIN classes c ON i.classe_id = c.id
                LEFT JOIN options o ON c.option_id = o.id
                LEFT JOIN annees_academiques aa ON i.annee_id = aa.id
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}