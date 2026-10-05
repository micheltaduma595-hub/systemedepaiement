<?php
/**
 * Script à exécuter quotidiennement via tâche planifiée (cron)
 * Vérifie les échéances et envoie les notifications appropriées
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/NotificationService.php';

echo "=== VÉRIFICATION DES ÉCHÉANCES ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "==================================\n\n";

try {
    $service = new NotificationService();
    $resultats = $service->verifierEtEnvoyerRappels();

    echo "RÉSULTATS :\n";
    echo "- Rappels J-5 envoyés : {$resultats['rappels_5_jours']}\n";
    echo "- Rappels J-3 envoyés : {$resultats['rappels_3_jours']}\n";
    echo "- Rappels J-1 envoyés : {$resultats['rappels_1_jour']}\n";
    echo "- Notifications insolvables : {$resultats['notifications_insolvables']}\n";
    echo "- Félicitations solvables : {$resultats['felicitations_solvables']}\n";
    echo "\nTotal notifications : " . array_sum($resultats) . "\n";

} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";
