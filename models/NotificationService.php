<?php
require_once __DIR__ . '/EmailService.php';
require_once __DIR__ . '/../config/database.php';

class NotificationService {
    private $emailService;
    private $db;
    
    public function __construct() {
        $this->emailService = new EmailService();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * ============================================
     * NOTIFICATIONS D'INSCRIPTION
     * ============================================
     */
    public function notifierInscription($eleveId) {
        $sql = "SELECT e.*, c.nom as classe_nom, c.niveau, c.id as classe_id,
                       o.nom as option_nom, i.id as inscription_id,
                       aa.libelle as annee_libelle
                FROM eleves e
                JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'actif'
                JOIN classes c ON i.classe_id = c.id
                JOIN options o ON c.option_id = o.id
                JOIN annees_academiques aa ON i.annee_id = aa.id
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eleveId]);
        $eleve = $stmt->fetch();
        
        if (!$eleve || empty($eleve['email_parent'])) return false;
        
        // Récupérer les frais de la classe
        $frais = $this->getFraisByClasse($eleve['classe_id']);
        
        // Récupérer le calendrier actif
        $calendrier = $this->getCalendrierActif($eleve['classe_id']);
        
        $classe = [
            'nom' => $eleve['classe_nom'],
            'niveau' => $eleve['niveau'],
            'option_nom' => $eleve['option_nom']
        ];
        
        $result = $this->emailService->envoyerEmailInscription($eleve, $classe, $frais, $calendrier);
        $this->logNotification($eleve['id'], $eleve['email_parent'], 'inscription', 'Email de bienvenue envoyé', $result['success'] ?? false);
        
        return $result;
    }
    
    /**
     * ============================================
     * NOTIFICATIONS DE PAIEMENT
     * ============================================
     */
   /**
 * Notifier après un paiement
 */
public function notifierPaiement($paiementId) {
    $sql = "SELECT p.*, e.*, f.libelle as frais_libelle, 
                   tf.libelle as type_frais, f.montant as frais_montant,
                   c.nom as classe_nom, i.id as inscription_id
            FROM paiements p
            JOIN inscriptions i ON p.inscription_id = i.id
            JOIN eleves e ON i.eleve_id = e.id
            JOIN frais f ON p.frais_id = f.id
            JOIN types_frais tf ON f.type_frais_id = tf.id
            JOIN classes c ON i.classe_id = c.id
            WHERE p.id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$paiementId]);
    $data = $stmt->fetch();
    
    if (!$data) return false;
    
    // Calculer le solde
    $totalFrais = $this->getTotalFrais($data['inscription_id']);
    $totalPaye = $this->getTotalPaye($data['inscription_id']);
    $solde = $totalFrais - $totalPaye;
    
    // Vérifier si l'élève est maintenant solvable
    $estSolvable = ($solde <= 0);
    
    // Envoyer l'email de confirmation de paiement
    $result = $this->emailService->envoyerEmailPaiement($data, $data, $solde);
    
    // Si l'élève devient solvable, envoyer un email de félicitations
    if ($estSolvable) {
        // Utiliser la méthode qui existe dans EmailService
        if (method_exists($this->emailService, 'envoyerFelicitationsSolvabilite')) {
            $this->emailService->envoyerFelicitationsSolvabilite($data);
        }
    }
    
    // Logger les notifications
    $emails = [];
    if (!empty($data['email_parent'])) $emails[] = $data['email_parent'];
    if (!empty($data['email'])) $emails[] = $data['email'];
    
    foreach ($emails as $email) {
        $this->logNotification(
            $data['id'], 
            $email, 
            'paiement', 
            'Paiement de ' . number_format($data['montant_paye'], 2) . ' FC - Solde: ' . number_format($solde, 2) . ' FC', 
            true
        );
    }
    
    return $result;
}
    
    /**
     * ============================================
     * VÉRIFICATION DES ÉCHÉANCES ET RAPPELS
     * ============================================
     */
    
    /**
     * Méthode principale à exécuter quotidiennement
     * Vérifie les échéances à J-5, J-3, J-1 et le jour J
     */
    public function verifierEtEnvoyerRappels() {
        $rapports = [
            'rappels_5_jours' => 0,
            'rappels_3_jours' => 0,
            'rappels_1_jour' => 0,
            'notifications_insolvables' => 0,
            'felicitations_solvables' => 0
        ];
        
        // 1. Rappels à J-5 (avertissement)
        $rapports['rappels_5_jours'] = $this->envoyerRappelsEcheance(5);
        
        // 2. Rappels à J-3 (urgence)
        $rapports['rappels_3_jours'] = $this->envoyerRappelsEcheance(3);
        
        // 3. Rappels à J-1 (dernier jour)
        $rapports['rappels_1_jour'] = $this->envoyerRappelsEcheance(1);
        
        // 4. Vérifier les insolvables (date dépassée)
        $rapports['notifications_insolvables'] = $this->notifierInsolvables();
        
        // 5. Féliciter les élèves devenus solvables
        $rapports['felicitations_solvables'] = $this->feliciterSolvables();
        
        return $rapports;
    }
    
    /**
     * Envoyer des rappels pour les échéances dans X jours
     */
    private function envoyerRappelsEcheance($joursAvant) {
        $sql = "SELECT 
                    e.*, i.id as inscription_id, c.nom as classe_nom,
                    ct.libelle as tranche_libelle, ct.date_limite,
                    ct.pourcentage, cp.libelle as calendrier_libelle,
                    (SELECT COALESCE(SUM(f.montant), 0) FROM frais f WHERE f.classe_id = c.id AND f.statut = 'actif') as total_frais,
                    (SELECT COALESCE(SUM(p.montant_paye), 0) FROM paiements p WHERE p.inscription_id = i.id AND p.statut = 'valide') as total_paye
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN calendrier_paiement cp ON cp.annee_id = i.annee_id AND cp.actif = 1
                JOIN calendrier_tranches ct ON ct.calendrier_id = cp.id
                WHERE i.statut = 'actif'
                AND ct.date_limite = DATE_ADD(CURDATE(), INTERVAL {$joursAvant} DAY)
                AND e.email_parent IS NOT NULL 
                AND e.email_parent != ''";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $eleves = $stmt->fetchAll();
        
        $envoyes = 0;
        
        foreach ($eleves as $eleve) {
            // Calculer le montant attendu pour cette tranche
            $montantTranche = round($eleve['total_frais'] * $eleve['pourcentage'] / 100, 2);
            
            // Vérifier si l'élève a déjà payé assez pour cette tranche
            // On considère qu'il doit avoir payé le pourcentage correspondant
            $montantAttendu = $montantTranche;
            $montantRestant = $montantAttendu - $this->getMontantPayePourTranche($eleve['inscription_id'], $eleve['total_frais'], $eleve['pourcentage']);
            
            // Si le montant restant est positif, envoyer un rappel
            if ($montantRestant > 0) {
                $result = $this->emailService->envoyerRappelEcheance(
                    $eleve, 
                    $eleve['tranche_libelle'],
                    $montantRestant, 
                    $eleve['date_limite'], 
                    $joursAvant
                );
                
                if ($result['success'] ?? false) {
                    $envoyes++;
                    $this->logNotification($eleve['id'], $eleve['email_parent'], 'rappel', 
                        "Rappel J-{$joursAvant} - Tranche: {$eleve['tranche_libelle']} - Restant: " . number_format($montantRestant, 2) . ' FC', 
                        true);
                }
            }
        }
        
        return $envoyes;
    }
    
    /**
     * Notifier les élèves insolvables (date dépassée)
     */
    private function notifierInsolvables() {
        $sql = "SELECT 
                    e.*, i.id as inscription_id, c.nom as classe_nom,
                    ct.libelle as tranche_libelle, ct.date_limite,
                    ct.pourcentage, cp.libelle as calendrier_libelle,
                    (SELECT COALESCE(SUM(f.montant), 0) FROM frais f WHERE f.classe_id = c.id AND f.statut = 'actif') as total_frais,
                    (SELECT COALESCE(SUM(p.montant_paye), 0) FROM paiements p WHERE p.inscription_id = i.id AND p.statut = 'valide') as total_paye,
                    DATEDIFF(CURDATE(), ct.date_limite) as jours_depasses
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                JOIN calendrier_paiement cp ON cp.annee_id = i.annee_id AND cp.actif = 1
                JOIN calendrier_tranches ct ON ct.calendrier_id = cp.id
                WHERE i.statut = 'actif'
                AND ct.date_limite = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                AND e.email_parent IS NOT NULL 
                AND e.email_parent != ''";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $insolvables = $stmt->fetchAll();
        
        $envoyes = 0;
        
        foreach ($insolvables as $eleve) {
            $montantTranche = round($eleve['total_frais'] * $eleve['pourcentage'] / 100, 2);
            $montantRestant = $montantTranche - $this->getMontantPayePourTranche($eleve['inscription_id'], $eleve['total_frais'], $eleve['pourcentage']);
            
            if ($montantRestant > 0) {
                $result = $this->emailService->envoyerNotificationInsolvable(
                    $eleve,
                    $eleve['tranche_libelle'],
                    $montantRestant,
                    $eleve['jour_depasses']
                );
                
                if ($result['success'] ?? false) {
                    $envoyes++;
                    
                    // Marquer comme insolvable dans la base
                    $this->marquerInsolvable($eleve['inscription_id'], $eleve['id']);
                    
                    $this->logNotification($eleve['id'], $eleve['email_parent'], 'insolvable', 
                        "INSOLVABLE - Tranche: {$eleve['tranche_libelle']} - Dû: " . number_format($montantRestant, 2) . ' FC', 
                        true);
                }
            }
        }
        
        return $envoyes;
    }
    
    /**
     * Féliciter les élèves qui viennent de devenir solvables
     */
    private function feliciterSolvables() {
        // Trouver les élèves qui ont tout payé récemment
        $sql = "SELECT e.*, i.id as inscription_id, c.nom as classe_nom,
                       (SELECT COALESCE(SUM(f.montant), 0) FROM frais f WHERE f.classe_id = c.id AND f.statut = 'actif') as total_frais,
                       (SELECT COALESCE(SUM(p.montant_paye), 0) FROM paiements p WHERE p.inscription_id = i.id AND p.statut = 'valide') as total_paye,
                       (SELECT MAX(p2.date_paiement) FROM paiements p2 WHERE p2.inscription_id = i.id AND p2.statut = 'valide') as dernier_paiement
                FROM inscriptions i
                JOIN eleves e ON i.eleve_id = e.id
                JOIN classes c ON i.classe_id = c.id
                WHERE i.statut = 'actif'
                AND e.email_parent IS NOT NULL
                HAVING total_paye >= total_frais
                AND DATE(dernier_paiement) = CURDATE()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $solvables = $stmt->fetchAll();
        
        $envoyes = 0;
        
        foreach ($solvables as $eleve) {
            // Ne pas envoyer si déjà félicité aujourd'hui
            if (!$this->dejaNotifieAujourdhui($eleve['id'], 'felicitation')) {
                $result = $this->emailService->envoyerFelicitationsSolvabilite($eleve);
                
                if ($result['success'] ?? false) {
                    $envoyes++;
                    $this->logNotification($eleve['id'], $eleve['email_parent'], 'felicitation', 
                        'Félicitations - Tous les frais sont payés !', 
                        true);
                }
            }
        }
        
        return $envoyes;
    }
    
    /**
     * ============================================
     * MÉTHODES UTILITAIRES
     * ============================================
     */
    
    private function getFraisByClasse($classeId) {
        $stmt = $this->db->prepare("
            SELECT f.*, tf.libelle as type_frais_libelle 
            FROM frais f 
            JOIN types_frais tf ON f.type_frais_id = tf.id 
            WHERE f.classe_id = ? AND f.statut = 'actif'
        ");
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }
    
    private function getCalendrierActif($classeId) {
        $stmt = $this->db->prepare("
            SELECT cp.*, ct.*
            FROM calendrier_paiement cp
            JOIN calendrier_tranches ct ON cp.id = ct.calendrier_id
            JOIN classes c ON c.id = ?
            JOIN inscriptions i ON i.classe_id = c.id
            WHERE cp.annee_id = i.annee_id AND cp.actif = 1
            ORDER BY ct.date_limite ASC
        ");
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }
    
    private function getTotalFrais($inscriptionId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(f.montant), 0) 
            FROM frais f 
            JOIN inscriptions i ON f.classe_id = i.classe_id 
            WHERE i.id = ? AND f.statut = 'actif'
        ");
        $stmt->execute([$inscriptionId]);
        return $stmt->fetchColumn();
    }
    
    private function getTotalPaye($inscriptionId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(montant_paye), 0) 
            FROM paiements 
            WHERE inscription_id = ? AND statut = 'valide'
        ");
        $stmt->execute([$inscriptionId]);
        return $stmt->fetchColumn();
    }
    
    private function getMontantPayePourTranche($inscriptionId, $totalFrais, $pourcentage) {
        $montantAttendu = round($totalFrais * $pourcentage / 100, 2);
        
        // On considère que l'élève doit avoir payé au moins le pourcentage de la tranche
        $totalPaye = $this->getTotalPaye($inscriptionId);
        
        // Retourner le montant restant à payer pour cette tranche
        return max(0, $montantAttendu - $totalPaye);
    }
    
    private function marquerInsolvable($inscriptionId, $eleveId) {
        // Vérifier si déjà marqué aujourd'hui
        $stmt = $this->db->prepare("
            SELECT id FROM notifications 
            WHERE type = 'insolvable' 
            AND destinataire_email = (SELECT email_parent FROM eleves WHERE id = ?)
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$eleveId]);
        
        if (!$stmt->fetch()) {
            // Insérer dans la table notifications
            $stmt = $this->db->prepare("
                INSERT INTO notifications (type, destinataire_email, sujet, message, envoye, date_envoi) 
                VALUES ('insolvable', 
                    (SELECT email_parent FROM eleves WHERE id = ?),
                    'Élève insolvable',
                    'Date de paiement dépassée',
                    1, NOW())
            ");
            $stmt->execute([$eleveId]);
        }
    }
    
    private function dejaNotifieAujourdhui($eleveId, $type) {
        $stmt = $this->db->prepare("
            SELECT id FROM notifications 
            WHERE type = ? 
            AND DATE(created_at) = CURDATE()
            AND message LIKE CONCAT('%', (SELECT CONCAT(nom, ' ', prenom) FROM eleves WHERE id = ?), '%')
        ");
        $stmt->execute([$type, $eleveId]);
        return $stmt->fetch() ? true : false;
    }
    
    public function logNotification($eleveId, $email, $type, $message, $succes) {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (type, destinataire_email, sujet, message, envoye, date_envoi) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $type,
            $email,
            'Notification automatique',
            $message,
            $succes ? 1 : 0
        ]);
    }
}