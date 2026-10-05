<?php
require_once __DIR__ . '/Model.php';

class Paiement extends Model {
    protected $table = 'paiements';
    
    public function getAllWithDetails() {
        $sql = "SELECT p.*, 
                       e.matricule, e.nom, e.postnom, e.prenom,
                       c.nom as classe_nom, c.niveau,
                       f.libelle as frais_libelle, f.montant as frais_montant,
                       tf.libelle as type_frais,
                       u.nom as caissier_nom
                FROM paiements p
                JOIN inscriptions i ON p.inscription_id = i.id
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN frais f ON p.frais_id = f.id
                JOIN types_frais tf ON f.type_frais_id = tf.id
                LEFT JOIN utilisateurs u ON p.created_by = u.id
                ORDER BY p.date_paiement DESC
                LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPaiementsByPeriode($dateDebut, $dateFin) {
        $sql = "SELECT p.*, e.matricule, e.nom, e.prenom,
                       c.nom as classe_nom, f.libelle as frais_libelle
                FROM paiements p
                JOIN inscriptions i ON p.inscription_id = i.id
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN frais f ON p.frais_id = f.id
                WHERE DATE(p.date_paiement) BETWEEN :debut AND :fin
                AND p.statut = 'valide'
                ORDER BY p.date_paiement DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['debut' => $dateDebut, 'fin' => $dateFin]);
        return $stmt->fetchAll();
    }
    
    public function getPaiementsByEleve($eleveId) {
        $sql = "SELECT p.*, f.libelle as frais_libelle, f.montant as frais_montant,
                       tf.libelle as type_frais
                FROM paiements p
                JOIN inscriptions i ON p.inscription_id = i.id
                JOIN frais f ON p.frais_id = f.id
                JOIN types_frais tf ON f.type_frais_id = tf.id
                WHERE i.eleve_id = :eleve_id
                AND p.statut = 'valide'
                ORDER BY p.date_paiement DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['eleve_id' => $eleveId]);
        return $stmt->fetchAll();
    }
    
    public function getTotalPayeParInscription($inscriptionId) {
        $sql = "SELECT COALESCE(SUM(montant_paye), 0) as total
                FROM paiements
                WHERE inscription_id = :id AND statut = 'valide'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $inscriptionId]);
        return $stmt->fetch()['total'];
    }
    
    public function getSoldeRestant($inscriptionId) {
        $sql = "SELECT 
                    COALESCE((SELECT SUM(f.montant) 
                     FROM frais f 
                     WHERE f.classe_id = i.classe_id AND f.statut = 'actif'), 0) -
                    COALESCE((SELECT SUM(p.montant_paye) 
                     FROM paiements p 
                     WHERE p.inscription_id = i.id AND p.statut = 'valide'), 0) as solde
                FROM inscriptions i
                WHERE i.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $inscriptionId]);
        return $stmt->fetch()['solde'];
    }
    
    public function getPaiementDetail($id) {
        $sql = "SELECT p.*, 
                       e.matricule, e.nom, e.postnom, e.prenom,
                       e.email, e.telephone,
                       c.nom as classe_nom, c.niveau,
                       o.nom as option_nom,
                       f.libelle as frais_libelle, f.montant as frais_montant,
                       tf.libelle as type_frais,
                       cp.libelle as echeance,
                       u.nom as caissier_nom
                FROM paiements p
                JOIN inscriptions i ON p.inscription_id = i.id
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN options o ON c.option_id = o.id
                JOIN frais f ON p.frais_id = f.id
                JOIN types_frais tf ON f.type_frais_id = tf.id
                LEFT JOIN calendrier_paiement cp ON f.calendrier_id = cp.id
                LEFT JOIN utilisateurs u ON p.created_by = u.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getRapportJournalier($date) {
        $sql = "SELECT 
                    p.mode_paiement,
                    COUNT(*) as nombre,
                    SUM(p.montant_paye) as total
                FROM paiements p
                WHERE DATE(p.date_paiement) = :date
                AND p.statut = 'valide'
                GROUP BY p.mode_paiement";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }
    
    public function getStatistiques() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COALESCE(SUM(montant_paye), 0) FROM paiements WHERE statut = 'valide'");
        $stats['total_paiements'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COALESCE(SUM(montant_paye), 0) FROM paiements WHERE DATE(date_paiement) = CURDATE() AND statut = 'valide'");
        $stats['paiements_jour'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM paiements WHERE statut = 'valide'");
        $stats['nombre_transactions'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT mode_paiement, COUNT(*) as nb, SUM(montant_paye) as total FROM paiements WHERE statut = 'valide' GROUP BY mode_paiement");
        $stats['par_mode'] = $stmt->fetchAll();
        
        return $stats;
    }
}