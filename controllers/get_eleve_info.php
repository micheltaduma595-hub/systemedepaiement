<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$inscription_id = isset($_GET['inscription_id']) ? intval($_GET['inscription_id']) : 0;

if ($inscription_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inscription invalide']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, 
                   i.id as inscription_id,
                   c.id as classe_id,
                   c.nom as classe_nom, 
                   c.niveau,
                   o.nom as option_nom
            FROM inscriptions i
            JOIN eleves e ON i.eleve_id = e.id
            JOIN classes c ON i.classe_id = c.id
            JOIN options o ON c.option_id = o.id
            WHERE i.id = ? AND i.statut = 'actif'";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$inscription_id]);
    $eleve = $stmt->fetch();
    
    if (!$eleve) {
        echo json_encode(['success' => false, 'message' => 'Élève non trouvé']);
        exit;
    }
    
    // Calculer les totaux
    $stmt = $db->prepare("SELECT COALESCE(SUM(montant), 0) FROM frais WHERE classe_id = ? AND statut = 'actif'");
    $stmt->execute([$eleve['classe_id']]);
    $total_frais = $stmt->fetchColumn();
    
    $stmt = $db->prepare("SELECT COALESCE(SUM(montant_paye), 0) FROM paiements WHERE inscription_id = ? AND statut = 'valide'");
    $stmt->execute([$inscription_id]);
    $total_paye = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'eleve' => [
            'id' => $eleve['id'],
            'matricule' => $eleve['matricule'],
            'nom_complet' => $eleve['nom'] . ' ' . $eleve['prenom'],
            'classe_id' => $eleve['classe_id'],
            'classe' => $eleve['classe_nom'] . ' (' . $eleve['niveau'] . ')',
            'option' => $eleve['option_nom']
        ],
        'total_frais' => number_format($total_frais, 2),
        'total_paye' => number_format($total_paye, 2),
        'solde' => number_format($total_frais - $total_paye, 2)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}