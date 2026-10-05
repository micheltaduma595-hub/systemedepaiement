<?php
require_once __DIR__ . '/Model.php';

class Calendrier extends Model {
    protected $table = 'calendrier_paiement';
    
    /**
     * Récupérer tous les calendriers avec leurs tranches
     */
    public function getAllWithTranches() {
        $sql = "SELECT cp.*, 
                       aa.libelle as annee_libelle,
                       COUNT(ct.id) as nombre_tranches,
                       MIN(ct.pourcentage) as debut_pourcentage,
                       MAX(ct.pourcentage) as fin_pourcentage
                FROM calendrier_paiement cp
                JOIN annees_academiques aa ON cp.annee_id = aa.id
                LEFT JOIN calendrier_tranches ct ON cp.id = ct.calendrier_id
                GROUP BY cp.id
                ORDER BY cp.date_debut DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer un calendrier avec toutes ses tranches
     */
    public function getWithTranches($id) {
        $calendrier = $this->findById($id);
        
        if ($calendrier) {
            $stmt = $this->db->prepare("
                SELECT * FROM calendrier_tranches 
                WHERE calendrier_id = ? 
                ORDER BY pourcentage ASC
            ");
            $stmt->execute([$id]);
            $calendrier['tranches'] = $stmt->fetchAll();
        }
        
        return $calendrier;
    }
    
    /**
     * Créer un calendrier avec ses tranches
     */
    public function createWithTranches($data) {
        $tranches = $data['tranches'] ?? [];
        unset($data['tranches']);
        
        $this->db->beginTransaction();
        try {
            // Créer le calendrier
            $calendrierId = $this->create([
                'libelle' => $data['libelle'],
                'description' => $data['description'] ?? null,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'annee_id' => $data['annee_id'],
                'actif' => 1
            ]);
            
            // Créer les tranches
            if (!empty($tranches)) {
                $stmt = $this->db->prepare("
                    INSERT INTO calendrier_tranches (calendrier_id, numero, libelle, pourcentage, date_limite, description) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                foreach ($tranches as $index => $tranche) {
                    $stmt->execute([
                        $calendrierId,
                        $index + 1,
                        $tranche['libelle'],
                        $tranche['pourcentage'],
                        $tranche['date_limite'],
                        $tranche['description'] ?? null
                    ]);
                }
            }
            
            $this->db->commit();
            return $calendrierId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Mettre à jour un calendrier et ses tranches
     */
    public function updateWithTranches($id, $data) {
        $tranches = $data['tranches'] ?? [];
        unset($data['tranches']);
        
        $this->db->beginTransaction();
        try {
            // Mettre à jour le calendrier
            $this->update($id, [
                'libelle' => $data['libelle'],
                'description' => $data['description'] ?? null,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'annee_id' => $data['annee_id']
            ]);
            
            // Supprimer les anciennes tranches
            $stmt = $this->db->prepare("DELETE FROM calendrier_tranches WHERE calendrier_id = ?");
            $stmt->execute([$id]);
            
            // Recréer les tranches
            if (!empty($tranches)) {
                $stmt = $this->db->prepare("
                    INSERT INTO calendrier_tranches (calendrier_id, numero, libelle, pourcentage, date_limite, description) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                foreach ($tranches as $index => $tranche) {
                    $stmt->execute([
                        $id,
                        $index + 1,
                        $tranche['libelle'],
                        $tranche['pourcentage'],
                        $tranche['date_limite'],
                        $tranche['description'] ?? null
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Récupérer les calendriers actifs pour une année
     */
    public function getActifsByAnnee($anneeId) {
        return $this->findAll(
            ['annee_id' => $anneeId, 'actif' => 1],
            'date_debut ASC'
        );
    }
    
    /**
     * Récupérer les tranches actives pour le paiement
     */
    public function getTranchesActives($calendrierId) {
        $stmt = $this->db->prepare("
            SELECT * FROM calendrier_tranches 
            WHERE calendrier_id = ? 
            AND date_limite >= CURDATE()
            ORDER BY date_limite ASC
        ");
        $stmt->execute([$calendrierId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Calculer le montant dû par tranche pour une classe
     */
    public function getMontantParTranche($calendrierId, $classeId) {
        $stmt = $this->db->prepare("
            SELECT ct.*, 
                   (SELECT COALESCE(SUM(f.montant), 0) 
                    FROM frais f 
                    WHERE f.classe_id = ? AND f.statut = 'actif') as total_frais,
                   ROUND((SELECT COALESCE(SUM(f.montant), 0) 
                          FROM frais f 
                          WHERE f.classe_id = ? AND f.statut = 'actif') * ct.pourcentage / 100, 2) as montant_tranche
            FROM calendrier_tranches ct
            WHERE ct.calendrier_id = ?
            ORDER BY ct.numero ASC
        ");
        $stmt->execute([$classeId, $classeId, $calendrierId]);
        return $stmt->fetchAll();
    }
}