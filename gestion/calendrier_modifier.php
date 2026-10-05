<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/GestionController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID calendrier invalide';
    header('Location: ' . SITE_URL . '/gestion/calendriers.php');
    exit;
}

$controller = new GestionController();
$controller->detailsCalendrier($id);