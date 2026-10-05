<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecole_paiement_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Modification ici : Le nom de l'école est désormais en majuscules
define('SCHOOL_NAME', 'INSTITUT TECHNIQUE SAINT COMBONI');

define('SCHOOL_EMAIL', 'kaserekakawanehemiee@gmail.com');
define('SCHOOL_PHONE', '+243980443246');
define('SCHOOL_ADDRESS', 'butembo ville');
define('SCHOOL_LOGO', 'logo.jpg');
define('PRIMARY_COLOR', '#031833');
define('SITE_URL', 'http://localhost/systemedepaiement');

date_default_timezone_set('Africa/Lubumbashi');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);