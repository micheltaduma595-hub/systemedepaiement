<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/GestionController.php';
$controller = new GestionController();
$controller->calendriers();
