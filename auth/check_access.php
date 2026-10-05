<?php
/**
 * Contrôle d'accès - À inclure en haut de chaque page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/permissions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

// Déterminer le module actuel
$currentModule = basename(dirname($_SERVER['PHP_SELF']));
$currentFile = basename($_SERVER['PHP_SELF']);

// Mapping des fichiers vers les modules
$moduleMapping = [
    'dashboard.php' => 'dashboard',
    'eleves.php' => 'eleves',
    'paiements.php' => 'paiements',
    'solvabilite.php' => 'rapports',
    'options.php' => 'options',
    'classes.php' => 'classes',
    'frais.php' => 'frais',
    'types_frais.php' => 'types_frais',
    'calendriers.php' => 'calendriers',
    'annees.php' => 'annees',
    'utilisateurs.php' => 'utilisateurs',
    'parametres.php' => 'parametres',
];

// Déterminer le module
$module = $moduleMapping[$currentFile] ?? $currentModule;

// Déterminer l'action
$action = 'view';
if (strpos($currentFile, 'ajouter') !== false || strpos($currentFile, 'add') !== false) {
    $action = 'add';
} elseif (strpos($currentFile, 'modifier') !== false || strpos($currentFile, 'edit') !== false) {
    $action = 'edit';
} elseif (strpos($currentFile, 'supprimer') !== false || strpos($currentFile, 'delete') !== false) {
    $action = 'delete';
} elseif (strpos($currentFile, 'export') !== false) {
    $action = 'export';
}

// Vérifier la permission
if (!userCan($module, $action)) {
    // Si c'est une requête AJAX, retourner une erreur JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Permission refusée']);
        exit;
    }
    
    // Sinon, rediriger vers la page d'accès refusé
    header('Location: ' . SITE_URL . '/access_denied.php');
    exit;
}