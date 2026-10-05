<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../models/Classe.php';
require_once __DIR__ . '/../models/Frais.php';
require_once __DIR__ . '/../models/AnneeAcademique.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class GestionController extends Controller {
    private $optionModel;
    private $classeModel;
    private $fraisModel;
    private $anneeModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        $this->optionModel = new Option();
        $this->classeModel = new Classe();
        $this->fraisModel = new Frais();
        $this->anneeModel = new AnneeAcademique();
        $this->userModel = new Utilisateur();
    }
    
    // ============ OPTIONS ============
    public function options() {
        $this->requireAuth();
        $options = $this->optionModel->getWithClasses();
        $this->render('gestion/options', ['options' => $options]);
    }
    
    public function ajouterOption() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->optionModel->create([
                'nom' => $_POST['nom'],
                'description' => $_POST['description'] ?? ''
            ]);
            $_SESSION['success'] = 'Option ajoutée avec succès';
            header('Location: ' . SITE_URL . '/gestion/options.php');
            exit;
        }
        
        $this->render('gestion/option_ajouter');
    }
    
    // ============ CLASSES ============
    public function classes() {
        $this->requireAuth();
        $classes = $this->classeModel->getAllWithDetails();
        $this->render('gestion/classes', ['classes' => $classes]);
    }
    
    public function ajouterClasse() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->classeModel->create([
                'nom' => $_POST['nom'],
                'niveau' => $_POST['niveau'],
                'option_id' => $_POST['option_id'],
                'capacite' => $_POST['capacite'] ?? 50
            ]);
            $_SESSION['success'] = 'Classe ajoutée avec succès';
            header('Location: ' . SITE_URL . '/gestion/classes.php');
            exit;
        }
        
        $options = $this->classeModel->getOptions();
        $this->render('gestion/classe_ajouter', ['options' => $options]);
    }
    
    // ============ FRAIS ============
    public function frais() {
        $this->requireAuth();
        $frais = $this->fraisModel->getAllWithDetails();
        $this->render('gestion/frais', ['frais' => $frais]);
    }
    
    public function ajouterFrais() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fraisModel->create([
                'type_frais_id' => $_POST['type_frais_id'],
                'classe_id' => $_POST['classe_id'],
                'semestre_id' => $_POST['semestre_id'],
                'calendrier_id' => !empty($_POST['calendrier_id']) ? $_POST['calendrier_id'] : null,
                'montant' => $_POST['montant'],
                'libelle' => $_POST['libelle'],
                'date_limite' => $_POST['date_limite'] ?? null,
                'statut' => 'actif'
            ]);
            $_SESSION['success'] = 'Frais ajouté avec succès';
            header('Location: ' . SITE_URL . '/gestion/frais.php');
            exit;
        }
        
        $typesFrais = $this->fraisModel->getTypesFrais();
        $classes = $this->fraisModel->getClasses();
        $semestres = $this->fraisModel->getSemestres();
        $calendriers = $this->fraisModel->getCalendriers();
        
        $this->render('gestion/frais_ajouter', [
            'typesFrais' => $typesFrais,
            'classes' => $classes,
            'semestres' => $semestres,
            'calendriers' => $calendriers
        ]);
    }
    
    // ============ TYPES DE FRAIS ============
    public function typesFrais() {
        $this->requireAuth();
        $types = $this->fraisModel->getTypesFrais();
        $this->render('gestion/types_frais', ['types' => $types]);
    }
    
    public function ajouterTypeFrais() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->db->prepare("INSERT INTO types_frais (libelle, description) VALUES (?, ?)");
            $stmt->execute([$_POST['libelle'], $_POST['description'] ?? '']);
            $_SESSION['success'] = 'Type de frais ajouté avec succès';
            header('Location: ' . SITE_URL . '/gestion/types_frais.php');
            exit;
        }
        
        $this->render('gestion/type_frais_ajouter');
    }
    
   // Remplacer les méthodes calendrier existantes par celles-ci :

// ============ CALENDRIER DE PAIEMENT (VERSION AMÉLIORÉE) ============
public function calendriers() {
    $this->requireAuth();
    require_once __DIR__ . '/../models/Calendrier.php';
    
    $calendrierModel = new Calendrier();
    $calendriers = $calendrierModel->getAllWithTranches();
    
    $this->render('gestion/calendriers', ['calendriers' => $calendriers]);
}

public function ajouterCalendrier() {
    $this->requireAuth();
    require_once __DIR__ . '/../models/Calendrier.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $calendrierModel = new Calendrier();
        
        try {
            // Préparer les données des tranches
            $tranches = [];
            $noms = $_POST['tranche_nom'] ?? [];
            $pourcentages = $_POST['tranche_pourcentage'] ?? [];
            $dates = $_POST['tranche_date'] ?? [];
            $descriptions = $_POST['tranche_description'] ?? [];
            
            $totalPourcentage = 0;
            for ($i = 0; $i < count($noms); $i++) {
                if (!empty($noms[$i]) && !empty($dates[$i])) {
                    $pourcentage = floatval($pourcentages[$i]);
                    $totalPourcentage += $pourcentage;
                    
                    $tranches[] = [
                        'libelle' => $noms[$i],
                        'pourcentage' => $pourcentage,
                        'date_limite' => $dates[$i],
                        'description' => $descriptions[$i] ?? ''
                    ];
                }
            }
            
            // Vérifier que le total fait 100%
            if (abs($totalPourcentage - 100) > 0.01 && !empty($tranches)) {
                throw new Exception("Le total des pourcentages doit être égal à 100% (actuellement : {$totalPourcentage}%)");
            }
            
            $calendrierId = $calendrierModel->createWithTranches([
                'libelle' => $_POST['libelle'],
                'description' => $_POST['description'] ?? '',
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'annee_id' => $_POST['annee_id'],
                'tranches' => $tranches
            ]);
            
            $_SESSION['success'] = 'Calendrier de paiement créé avec ' . count($tranches) . ' tranche(s)';
            header('Location: ' . SITE_URL . '/gestion/calendriers.php');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
        }
    }
    
    $annees = $this->db->query("SELECT * FROM annees_academiques ORDER BY date_debut DESC")->fetchAll();
    $this->render('gestion/calendrier_ajouter', ['annees' => $annees]);
}

public function modifierCalendrier($id) {
    $this->requireAuth();
    require_once __DIR__ . '/../models/Calendrier.php';
    
    $calendrierModel = new Calendrier();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Même logique que l'ajout
            $tranches = [];
            $noms = $_POST['tranche_nom'] ?? [];
            $pourcentages = $_POST['tranche_pourcentage'] ?? [];
            $dates = $_POST['tranche_date'] ?? [];
            $descriptions = $_POST['tranche_description'] ?? [];
            
            $totalPourcentage = 0;
            for ($i = 0; $i < count($noms); $i++) {
                if (!empty($noms[$i]) && !empty($dates[$i])) {
                    $pourcentage = floatval($pourcentages[$i]);
                    $totalPourcentage += $pourcentage;
                    
                    $tranches[] = [
                        'libelle' => $noms[$i],
                        'pourcentage' => $pourcentage,
                        'date_limite' => $dates[$i],
                        'description' => $descriptions[$i] ?? ''
                    ];
                }
            }
            
            if (abs($totalPourcentage - 100) > 0.01 && !empty($tranches)) {
                throw new Exception("Le total des pourcentages doit être égal à 100%");
            }
            
            $calendrierModel->updateWithTranches($id, [
                'libelle' => $_POST['libelle'],
                'description' => $_POST['description'] ?? '',
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'annee_id' => $_POST['annee_id'],
                'tranches' => $tranches
            ]);
            
            $_SESSION['success'] = 'Calendrier mis à jour avec succès';
            header('Location: ' . SITE_URL . '/gestion/calendriers.php');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
        }
    }
    
    $calendrier = $calendrierModel->getWithTranches($id);
    $annees = $this->db->query("SELECT * FROM annees_academiques ORDER BY date_debut DESC")->fetchAll();
    
    $this->render('gestion/calendrier_modifier', [
        'calendrier' => $calendrier,
        'annees' => $annees
    ]);
}

public function detailsCalendrier($id) {
    $this->requireAuth();
    require_once __DIR__ . '/../models/Calendrier.php';
    
    $calendrierModel = new Calendrier();
    $calendrier = $calendrierModel->getWithTranches($id);
    
    if (!$calendrier) {
        $_SESSION['error'] = 'Calendrier non trouvé';
        header('Location: ' . SITE_URL . '/gestion/calendriers.php');
        exit;
    }
    
    $this->render('gestion/calendrier_details', ['calendrier' => $calendrier]);
}
     public function annees() {
        $this->requireAuth();
        $annees = $this->anneeModel->getAll();
        $this->render('gestion/annees', ['annees' => $annees]);
    }
    
    public function ajouterAnnee() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->anneeModel->create([
                'libelle' => $_POST['libelle'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'actif' => isset($_POST['actif']) ? 1 : 0
            ]);
            
            // Si c'est l'année active, désactiver les autres
            if (isset($_POST['actif'])) {
                $id = $this->db->lastInsertId();
                $this->anneeModel->activer($id);
            }
            
            $_SESSION['success'] = 'Année académique ajoutée avec succès';
            header('Location: ' . SITE_URL . '/gestion/annees.php');
            exit;
        }
        
        $this->render('gestion/annee_ajouter');
    }
    
    public function activerAnnee($id) {
        $this->requireAuth();
        $this->anneeModel->activer($id);
        $_SESSION['success'] = 'Année académique activée avec succès';
        header('Location: ' . SITE_URL . '/gestion/annees.php');
        exit;
    }
    
    public function detailsAnnee($id) {
        $this->requireAuth();
        $annee = $this->anneeModel->getWithStats($id);
        $this->render('gestion/annee_details', ['annee' => $annee]);
    }
    
    // ============ UTILISATEURS ============
    public function utilisateurs() {
        $this->requireAuth();
        $utilisateurs = $this->userModel->getAllWithRoles();
        $this->render('gestion/utilisateurs', ['utilisateurs' => $utilisateurs]);
    }
    
    public function ajouterUtilisateur() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'nom' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'mot_de_passe' => $_POST['mot_de_passe'],
                    'telephone' => $_POST['telephone'] ?? null,
                    'actif' => 1,
                    'roles' => $_POST['roles'] ?? []
                ];
                
                $this->userModel->createUser($data);
                $_SESSION['success'] = 'Utilisateur créé avec succès';
                header('Location: ' . SITE_URL . '/gestion/utilisateurs.php');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
            }
        }
        
        $roles = $this->userModel->getRoles();
        $this->render('gestion/utilisateur_ajouter', ['roles' => $roles]);
    }
    
    public function modifierUtilisateur($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'nom' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'telephone' => $_POST['telephone'] ?? null,
                    'actif' => isset($_POST['actif']) ? 1 : 0,
                    'roles' => $_POST['roles'] ?? []
                ];
                
                if (!empty($_POST['mot_de_passe'])) {
                    $data['mot_de_passe'] = $_POST['mot_de_passe'];
                }
                
                $this->userModel->updateUser($id, $data);
                $_SESSION['success'] = 'Utilisateur modifié avec succès';
                header('Location: ' . SITE_URL . '/gestion/utilisateurs.php');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
            }
        }
        
        $utilisateur = $this->userModel->getUserWithRoles($id);
        $roles = $this->userModel->getRoles();
        
        $this->render('gestion/utilisateur_modifier', [
            'utilisateur' => $utilisateur,
            'roles' => $roles
        ]);
    }
    
    public function logs() {
        $this->requireAuth();
        $logs = $this->userModel->getLogsRecents(100);
        $this->render('gestion/logs', ['logs' => $logs]);
    }
    
}