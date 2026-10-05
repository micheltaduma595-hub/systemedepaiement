<?php
require_once __DIR__ . '/Model.php';

class AnneeAcademique extends Model {
    protected $table = 'annees_academiques';
    
    public function getAll() {
        return $this->findAll([], 'date_debut DESC');
    }
    
    public function getActive() {
        return $this->findAll(['actif' => 1], 'date_debut DESC');
    }
    
    public function activer($id) {
        // Désactiver toutes les années
        $this->db->exec("UPDATE annees_academiques SET actif = 0");
        
        // Activer l'année sélectionnée
        return $this->update($id, ['actif' => 1]);
    }
    
    public function getWithStats($id) {
        $sql = "SELECT aa.*,
                       COUNT(DISTINCT i.id) as nombre_inscriptions,
                       COUNT(DISTINCT e.id) as nombre_eleves,
                       COALESCE(SUM(p.montant_paye), 0) as total_paiements
                FROM annees_academiques aa
                LEFT JOIN inscriptions i ON aa.id = i.annee_id
                LEFT JOIN eleves e ON i.eleve_id = e.id
                LEFT JOIN paiements p ON i.id = p.inscription_id AND p.statut = 'valide'
                WHERE aa.id = :id
                GROUP BY aa.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}