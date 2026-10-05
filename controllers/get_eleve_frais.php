<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$inscription_id = $_GET['inscription_id'] ?? 0;

try {
    $db = Database::getInstance()->getConnection();
    
    // Frais de la classe
    $stmt = $db->prepare("
        SELECT f.*, tf.libelle as type_libelle,
               cp.libelle as echeance, cp.date_fin
        FROM inscriptions i
        JOIN frais f ON f.classe_id = i.classe_id
        JOIN types_frais tf ON f.type_frais_id = tf.id
        LEFT JOIN calendrier_paiement cp ON f.calendrier_id = cp.id
        WHERE i.id = ? AND f.statut = 'actif'
    ");
    $stmt->execute([$inscription_id]);
    $frais = $stmt->fetchAll();
    
    // Totaux
    $stmt = $db->prepare("SELECT COALESCE(SUM(montant), 0) FROM frais WHERE classe_id = (SELECT classe_id FROM inscriptions WHERE id = ?) AND statut = 'actif'");
    $stmt->execute([$inscription_id]);
    $total_frais = $stmt->fetchColumn();
    
    $stmt = $db->prepare("SELECT COALESCE(SUM(montant_paye), 0) FROM paiements WHERE inscription_id = ? AND statut = 'valide'");
    $stmt->execute([$inscription_id]);
    $total_paye = $stmt->fetchColumn();
    
    $solde = $total_frais - $total_paye;
    
    echo json_encode([
        'success' => true,
        'frais' => $frais,
        'total_frais' => number_format($total_frais, 2),
        'total_paye' => number_format($total_paye, 2),
        'solde' => number_format($solde, 2)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}