<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/EleveController.php';

$controller = new EleveController();
$controller->index();