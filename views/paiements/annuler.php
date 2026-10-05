<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/PaiementController.php';

$controller = new PaiementController();
$controller->annuler($_GET['id'] ?? 0);
