<?php
require_once 'config/config.php';
require_once 'config/database.php';
if(!isset($_SESSION['user_id'])){header('Location: auth/login.php');exit;}
$db = Database::getInstance()->getConnection();
$nbE = $db->query("SELECT COUNT(*) FROM eleves WHERE actif=1")->fetchColumn();
$nbP = $db->query("SELECT COALESCE(SUM(montant_paye),0) FROM paiements WHERE statut='valide'")->fetchColumn();
$pageTitle='Tableau de bord'; ob_start();
?>
<h2>Tableau de Bord</h2>
<div class="row mt-4">
<div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body"><h3><?php echo $nbE; ?></h3><p>Élèves</p></div></div></div>
<div class="col-md-4"><div class="card bg-success text-white"><div class="card-body"><h3><?php echo number_format($nbP,0); ?> FC</h3><p>Paiements totaux</p></div></div></div>
<div class="col-md-4"><div class="card bg-info text-white"><div class="card-body"><h3>Rapports</h3><a href="rapports/solvabilite.php" class="btn btn-light btn-sm">Voir solvabilité</a></div></div></div>
</div>
<?php $content=ob_get_clean(); require_once 'views/template.php'; ?>