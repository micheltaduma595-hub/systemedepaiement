<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/PaiementController.php';

$controller = new PaiementController();
$controller->store();