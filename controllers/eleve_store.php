<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/EleveController.php';

$controller = new EleveController();
$controller->store();