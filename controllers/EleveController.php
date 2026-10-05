<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Eleve.php';
require_once __DIR__ . '/../models/Inscription.php';
require_once __DIR__ . '/../models/Paiement.php';
require_once __DIR__ . '/../models/Frais.php';

class EleveController extends Controller {
    private $eleveModel;
    
    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        $this->eleveModel = new Eleve();
    }
    
    // Afficher la liste des élèves
    public function index() {
        $this->requireAuth();
        
        $eleves = $this->eleveModel->getAllWithInscription();
        
        $this->render('eleves/liste', [
            'eleves' => $eleves
        ]);
    }
    
    // Formulaire d'ajout
    public function ajouter() {
        $this->requireAuth();
        
        $classes = $this->getClasses();
        $annees = $this->getAnneesActives();
        
        $this->render('eleves/ajouter', [
            'classes' => $classes,
            'annees' => $annees
        ]);
    }
    
    // Enregistrer un nouvel élève
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // DÉMARRER LA TRANSACTION UNE SEULE FOIS
                $this->db->beginTransaction();
                
                // Générer matricule
                $matricule = $this->genererMatricule();
                
                // Insérer l'élève
                $eleveData = [
                    'matricule' => $matricule,
                    'nom' => strtoupper($_POST['nom']),
                    'postnom' => strtoupper(isset($_POST['postnom']) ? $_POST['postnom'] : ''),
                    'prenom' => ucwords(strtolower($_POST['prenom'])),
                    'genre' => $_POST['genre'],
                    'date_naissance' => isset($_POST['date_naissance']) ? $_POST['date_naissance'] : null,
                    'email' => isset($_POST['email']) ? $_POST['email'] : null,
                    'telephone' => isset($_POST['telephone']) ? $_POST['telephone'] : null,
                    'adresse' => isset($_POST['adresse']) ? $_POST['adresse'] : null,
                    'nom_parent' => isset($_POST['nom_parent']) ? $_POST['nom_parent'] : null,
                    'telephone_parent' => isset($_POST['telephone_parent']) ? $_POST['telephone_parent'] : null,
                    'email_parent' => isset($_POST['email_parent']) ? $_POST['email_parent'] : null
                ];
                
                $eleveId = $this->eleveModel->create($eleveData);
                
                // Créer l'inscription si une classe est sélectionnée
                $inscriptionCreee = false;
                if (!empty($_POST['classe_id']) && !empty($_POST['annee_id'])) {
                    $inscriptionModel = new Inscription();
                    $inscriptionModel->create([
                        'eleve_id' => $eleveId,
                        'classe_id' => $_POST['classe_id'],
                        'annee_id' => $_POST['annee_id'],
                        'date_inscription' => date('Y-m-d'),
                        'statut' => 'actif'
                    ]);
                    $inscriptionCreee = true;
                }
                
                // VALIDER LA TRANSACTION
                $this->db->commit();
                
                // Après la transaction, envoyer la notification
                if ($inscriptionCreee) {
                    try {
                        require_once __DIR__ . '/../models/NotificationService.php';
                        $notifService = new NotificationService();
                        $notifService->notifierInscription($eleveId);
                    } catch (Exception $e) {
                        // L'erreur de notification ne doit pas bloquer l'inscription
                        error_log("Erreur notification: " . $e->getMessage());
                    }
                }
                
                $_SESSION['success'] = 'Élève ajouté avec succès ! Matricule : ' . $matricule;
                header('Location: ' . SITE_URL . '/eleves.php');
                exit;
                
            } catch (Exception $e) {
                // ROLLBACK SEULEMENT SI UNE TRANSACTION EST ACTIVE
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
                header('Location: ' . SITE_URL . '/eleves/ajouter.php');
                exit;
            }
        }
    }
    
    // Voir fiche élève
    public function fiche($id) {
        $this->requireAuth();
        
        $eleve = $this->eleveModel->getEleveDetail($id);
        
        if (!$eleve) {
            $_SESSION['error'] = 'Élève non trouvé';
            header('Location: ' . SITE_URL . '/eleves.php');
            exit;
        }
        
        // Récupérer les paiements de l'élève
        $paiementModel = new Paiement();
        $paiements = $paiementModel->getPaiementsByEleve($id);
        
        // Récupérer les frais de sa classe
        $frais = [];
        if (isset($eleve['classe_id']) && $eleve['classe_id']) {
            $fraisModel = new Frais();
            $frais = $fraisModel->getFraisByClasse($eleve['classe_id']);
        }
        
        $this->render('eleves/fiche', [
            'eleve' => $eleve,
            'paiements' => $paiements,
            'frais' => $frais
        ]);
    }
    
    // Modifier un élève
    public function modifier($id) {
        $this->requireAuth();
        
        // Récupérer l'élève AVANT de vérifier la méthode POST
        $eleve = $this->eleveModel->findById($id);
        
        if (!$eleve) {
            $_SESSION['error'] = 'Élève non trouvé';
            header('Location: ' . SITE_URL . '/eleves.php');
            exit;
        }
        
        // Si c'est une soumission POST, on met à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => strtoupper($_POST['nom']),
                'postnom' => strtoupper($_POST['postnom'] ?? ''),
                'prenom' => ucwords(strtolower($_POST['prenom'])),
                'genre' => $_POST['genre'],
                'date_naissance' => !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null,
                'email' => !empty($_POST['email']) ? $_POST['email'] : null,
                'telephone' => !empty($_POST['telephone']) ? $_POST['telephone'] : null,
                'adresse' => !empty($_POST['adresse']) ? $_POST['adresse'] : null,
                'nom_parent' => !empty($_POST['nom_parent']) ? $_POST['nom_parent'] : null,
                'telephone_parent' => !empty($_POST['telephone_parent']) ? $_POST['telephone_parent'] : null,
                'email_parent' => !empty($_POST['email_parent']) ? $_POST['email_parent'] : null
            ];
            
            $this->eleveModel->update($id, $data);
            $_SESSION['success'] = 'Élève modifié avec succès !';
            header('Location: ' . SITE_URL . '/eleves.php');
            exit;
        }
        
        // Afficher le formulaire (GET)
        $this->render('eleves/modifier', ['eleve' => $eleve]);
    }
    
    // Supprimer un élève
    public function supprimer($id) {
        $this->requireAuth();
        
        // Désactiver plutôt que supprimer
        $this->eleveModel->update($id, ['actif' => 0]);
        $_SESSION['success'] = 'Élève supprimé avec succès !';
        header('Location: ' . SITE_URL . '/eleves.php');
        exit;
    }
    
    private function genererMatricule() {
        $annee = date('Y');
        $sql = "SELECT COUNT(*) as count FROM eleves WHERE matricule LIKE ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["ELE-{$annee}-%"]);
        $count = $stmt->fetch()['count'] + 1;
        return sprintf('ELE-%s-%05d', $annee, $count);
    }
    
    private function getClasses() {
        $sql = "SELECT c.id, c.nom, c.niveau, o.nom as option_nom 
                FROM classes c 
                JOIN options o ON c.option_id = o.id 
                WHERE c.actif = 1 
                ORDER BY c.nom";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    private function getAnneesActives() {
        $stmt = $this->db->query("SELECT * FROM annees_academiques WHERE actif = 1 ORDER BY date_debut DESC");
        return $stmt->fetchAll();
    }
}