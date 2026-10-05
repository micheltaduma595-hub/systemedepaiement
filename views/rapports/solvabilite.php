<?php
$pageTitle = 'Solvabilité';
ob_start();
?>
<h2><i class="fas fa-check-circle"></i> État de Solvabilité</h2>

<div class="row mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body"><h5>Total</h5><h3><?php echo count($data); ?></h3></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body"><h5>Solvables</h5><h3><?php echo count(array_filter($data, function($d){return $d['total_paye']>=$d['total_frais'];}));; ?></h3></div></div></div>
    <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body"><h5>Insolvables</h5><h3><?php echo count(array_filter($data, function($d){return $d['total_paye']<$d['total_frais'];}));; ?></h3></div></div></div>
</div>

<div class="card mb-3"><div class="card-body">
    <form method="GET" class="row">
        <div class="col-md-3"><select name="classe_id" class="form-select"><option value="">Toutes</option><?php foreach($classes as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($filters['classe_id']??'')==$c['id']?'selected':''; ?>><?php echo $c['nom']; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select name="statut" class="form-select"><option value="tous">Tous</option><option value="solvable" <?php echo ($filters['statut']??'')=='solvable'?'selected':''; ?>>Solvables</option><option value="insolvable" <?php echo ($filters['statut']??'')=='insolvable'?'selected':''; ?>>Insolvables</option></select></div>
        <div class="col-md-3"><button class="btn btn-primary">Filtrer</button>
            <a href="<?php echo SITE_URL; ?>/rapports/export_solvables.php" target="_blank" class="btn btn-success btn-sm">📄 Solvables</a>
            <a href="<?php echo SITE_URL; ?>/rapports/export_insolvables.php" target="_blank" class="btn btn-danger btn-sm">📄 Insolvables</a></div>
    </form>
</div></div>

<table id="t" class="table table-striped"><thead><tr><th>Matricule</th><th>Nom</th><th>Classe</th><th>Total</th><th>Payé</th><th>Solde</th><th>Statut</th></tr></thead><tbody>
<?php foreach($data as $r): $s=$r['total_frais']-$r['total_paye']; ?>
<tr><td><?php echo $r['matricule']; ?></td><td><?php echo $r['nom'].' '.$r['prenom']; ?></td><td><?php echo $r['classe']; ?></td>
<td class="text-end"><?php echo number_format($r['total_frais'],2); ?></td>
<td class="text-end"><?php echo number_format($r['total_paye'],2); ?></td>
<td class="text-end"><?php echo number_format($s,2); ?></td>
<td><span class="badge bg-<?php echo $s<=0?'success':'danger'; ?>"><?php echo $s<=0?'Solvable':'Insolvable'; ?></span></td></tr>
<?php endforeach; ?>
</tbody></table>
<script>$(function(){$('#t').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'}});});</script>
<?php $content = ob_get_clean(); require_once __DIR__.'/../template.php'; ?>