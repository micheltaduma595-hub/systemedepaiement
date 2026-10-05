<?php
require_once __DIR__ . '/Model.php';

class Inscription extends Model {
    protected $table = 'inscriptions';
    
    public function getInscriptionsActives() {
        $sql = "SELECT i.*, 
                       e.matricule, e.nom, e.postnom, e.prenom,
                       c.nom as classe_nom, c.niveau,
                       o.nom as option_nom,
                       aa.libelle as annee_academique
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN options o ON c.option_id = o.id
                JOIN annees_academiques aa ON i.annee_id = aa.id
                WHERE i.statut = 'actif'
                ORDER BY c.nom, e.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getInscriptionByEleve($eleveId) {
        $sql = "SELECT i.*, 
                       c.nom as classe_nom, c.niveau,
                       o.nom as option_nom,
                       aa.libelle as annee_academique
                FROM inscriptions i
                JOIN classes c ON i.classe_id = c.id
                JOIN options o ON c.option_id = o.id
                JOIN annees_academiques aa ON i.annee_id = aa.id
                WHERE i.eleve_id = :eleve_id AND i.statut = 'actif'
                ORDER BY i.date_inscription DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['eleve_id' => $eleveId]);
        return $stmt->fetch();
    }
}