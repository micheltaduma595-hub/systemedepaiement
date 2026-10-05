<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Paiement.php';
require_once __DIR__ . '/../models/Frais.php';
require_once __DIR__ . '/../models/Inscription.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Notification.php';

class PaiementController extends Controller {
    private $paiementModel;
    private $fraisModel;
    private $inscriptionModel;
    private $notificationModel;
    
    public function __construct() {
        parent::__construct(); // Maintenant ça fonctionne car Controller a un constructeur
        $this->paiementModel = new Paiement();
        $this->fraisModel = new Frais();
        $this->inscriptionModel = new Inscription();
        $this->notificationModel = new Notification();
    }
    
    // Liste des paiements
    public function index() {
        $this->requireAuth();
        
        $dateDebut = $_GET['date_debut'] ?? date('Y-m-01');
        $dateFin = $_GET['date_fin'] ?? date('Y-m-t');
        
        $paiements = $this->paiementModel->getAllWithDetails();
        $stats = $this->paiementModel->getStatistiques();
        
        $this->render('paiements/liste', [
            'paiements' => $paiements,
            'stats' => $stats,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);
    }
    
    // Formulaire nouveau paiement
    public function nouveau() {
        $this->requireAuth();
        
        $inscription_id = $_GET['inscription_id'] ?? null;
        $eleve = null;
        $frais = [];
        $totalPaye = 0;
        $totalFrais = 0;
        $solde = 0;
        
        if ($inscription_id) {
            $inscription = $this->inscriptionModel->findById($inscription_id);
            if ($inscription) {
                $eleveModel = new Eleve();
                $eleve = $eleveModel->findById($inscription['eleve_id']);
                $frais = $this->fraisModel->getFraisByClasse($inscription['classe_id']);
                
                // Calculer le total déjà payé
                $totalPaye = $this->paiementModel->getTotalPayeParInscription($inscription_id);
                $totalFrais = $this->fraisModel->getTotalFraisByClasse($inscription['classe_id']);
                $solde = $totalFrais - $totalPaye;
            }
        }
        
        $eleves = $this->getElevesActifs();
        
        $this->render('paiements/nouveau', [
            'inscription_id' => $inscription_id,
            'eleve' => $eleve,
            'frais' => $frais,
            'eleves' => $eleves,
            'totalPaye' => $totalPaye,
            'totalFrais' => $totalFrais,
            'solde' => $solde
        ]);
    }
    
    // Enregistrer un paiement
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
                
                // Calculer le solde
                $solde = $this->paiementModel->getSoldeRestant($_POST['inscription_id']);
                $montantPaye = floatval($_POST['montant_paye']);
                
                if ($montantPaye <= 0) {
                    throw new Exception("Le montant doit être supérieur à 0");
                }
                
                if ($montantPaye > $solde) {
                    throw new Exception("Le montant ne peut pas dépasser le solde restant (" . number_format($solde, 2) . " FC)");
                }
                
                // Générer un numéro de reçu temporaire
                $paiementData = [
                    'inscription_id' => $_POST['inscription_id'],
                    'frais_id' => $_POST['frais_id'],
                    'montant_paye' => $montantPaye,
                    'date_paiement' => date('Y-m-d H:i:s'),
                    'mode_paiement' => $_POST['mode_paiement'],
                    'reference' => $_POST['reference'] ?? null,
                    'commentaire' => $_POST['commentaire'] ?? null,
                    'statut' => 'valide',
                    'created_by' => $_SESSION['user_id']
                ];
                
                $paiementId = $this->paiementModel->create($paiementData);
                
                // Mettre à jour le numéro de reçu
                $recuNumero = 'RECU-' . date('Y') . '-' . str_pad($paiementId, 6, '0', STR_PAD_LEFT);
                $stmt = $this->db->prepare("UPDATE paiements SET recu_numero = ? WHERE id = ?");
                $stmt->execute([$recuNumero, $paiementId]);

                // ENVOYER LA NOTIFICATION DE PAIEMENT
                require_once __DIR__ . '/../models/NotificationService.php';
                $notifService = new NotificationService();
                $notifService->notifierPaiement($paiementId);
                
                // Envoyer notification si email parent disponible
                $inscription = $this->inscriptionModel->findById($_POST['inscription_id']);
                if ($inscription) {
                    $eleveModel = new Eleve();
                    $eleve = $eleveModel->findById($inscription['eleve_id']);
                    
                    if (!empty($eleve['email_parent'])) {
                        $nouveauSolde = $solde - $montantPaye;
                        $this->notificationModel->creerNotificationPaiement(
                            $eleve['email_parent'],
                            $montantPaye,
                            $nouveauSolde,
                            $eleve['nom'] . ' ' . $eleve['prenom']
                        );
                    }
                }
                
                $this->db->commit();
                $_SESSION['success'] = 'Paiement enregistré avec succès ! Reçu N° ' . $recuNumero;
                
                // Rediriger vers le reçu
                header('Location: ' . SITE_URL . '/paiements/recu.php?id=' . $paiementId);
                exit;
                
            } catch (Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
                header('Location: ' . SITE_URL . '/paiements/nouveau.php');
                exit;
            }
        }
    }
    
    // Afficher un reçu
    public function recu($id) {
        $this->requireAuth();
        
        $paiement = $this->paiementModel->getPaiementDetail($id);
        
        if (!$paiement) {
            $_SESSION['error'] = 'Paiement non trouvé';
            $this->redirect('paiements.php');
            return;
        }
        
        $this->render('paiements/recu', [
            'paiement' => $paiement
        ]);
    }
    
    // Annuler un paiement
    public function annuler($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->db->prepare("UPDATE paiements SET statut = 'annule', commentaire = ? WHERE id = ?");
            $stmt->execute([
                $_POST['motif'] ?? 'Annulé par ' . $_SESSION['user_name'],
                $id
            ]);
            
            $_SESSION['success'] = 'Paiement annulé avec succès';
            $this->redirect('paiements.php');
        }
    }
    
    // Rapport journalier
    public function rapportJournalier() {
        $this->requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $paiements = $this->paiementModel->getPaiementsByPeriode($date, $date);
        $resume = $this->paiementModel->getRapportJournalier($date);
        
        $totalJour = array_sum(array_column($resume, 'total'));
        
        $this->render('paiements/rapport_journalier', [
            'paiements' => $paiements,
            'resume' => $resume,
            'date' => $date,
            'totalJour' => $totalJour
        ]);
    }
    
    private function getElevesActifs() {
    $sql = "SELECT e.id, e.matricule, e.nom, e.prenom, 
                   c.nom as classe_nom, c.id as classe_id,
                   i.id as inscription_id
            FROM eleves e
            JOIN inscriptions i ON e.id = i.eleve_id AND i.statut = 'actif'
            JOIN classes c ON i.classe_id = c.id
            WHERE e.actif = 1
            ORDER BY e.nom, e.prenom";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}
    
}