<?php
require_once '../config/config.php';
require_once '../controllers/RapportsController.php';
$c = new RapportsController();
$c->exportPDF('insolvable');
