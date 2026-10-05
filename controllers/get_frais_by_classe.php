<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$classe_id = isset($_GET['classe_id']) ? intval($_GET['classe_id']) : 0;
$inscription_id = isset($_GET['inscription_id']) ? intval($_GET['inscription_id']) : 0;

if ($classe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID classe invalide']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT f.*, 
                   tf.libelle as type_frais_libelle,
                   s.nom as semestre_nom,
                   cp.libelle as echeance_libelle, 
                   cp.date_fin as echeance_fin,
                   COALESCE(
                       (SELECT SUM(p.montant_paye) 
                        FROM paiements p 
                        WHERE p.frais_id = f.id 
                        AND p.inscription_id = ? 
                        AND p.statut = 'valide'), 0
                   ) as deja_paye
            FROM frais f
            JOIN types_frais tf ON f.type_frais_id = tf.id
            JOIN semestres s ON f.semestre_id = s.id
            LEFT JOIN calendrier_paiement cp ON f.calendrier_id = cp.id
            WHERE f.classe_id = ? 
            AND f.statut = 'actif'
            ORDER BY f.date_limite, tf.libelle";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$inscription_id, $classe_id]);
    $frais = $stmt->fetchAll();
    
    $total_frais = 0;
    $total_paye = 0;
    
    $result = [];
    foreach ($frais as $f) {
        $reste = $f['montant'] - $f['deja_paye'];
        $total_frais += $f['montant'];
        $total_paye += $f['deja_paye'];
        
        $result[] = [
            'id' => $f['id'],
            'libelle' => $f['libelle'],
            'type_frais_libelle' => $f['type_frais_libelle'],
            'semestre_nom' => $f['semestre_nom'],
            'echeance_libelle' => $f['echeance_libelle'] ?? '',
            'date_limite' => $f['date_limite'] ?? '',
            'montant' => $f['montant'],
            'deja_paye' => $f['deja_paye'],
            'reste_a_payer' => $reste,
            'est_paye' => ($reste <= 0)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'frais' => $result,
        'total_frais' => number_format($total_frais, 2),
        'total_paye' => number_format($total_paye, 2),
        'solde' => number_format($total_frais - $total_paye, 2)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}