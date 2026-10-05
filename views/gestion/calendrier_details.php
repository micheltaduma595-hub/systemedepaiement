<?php
$pageTitle = "Détails Calendrier - " . ($calendrier['libelle'] ?? '');
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($calendrier['libelle'] ?? ''); ?></h2>
    <div>
        <a href="<?php echo SITE_URL; ?>/gestion/calendrier_modifier.php?id=<?php echo $calendrier['id']; ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="<?php echo SITE_URL; ?>/gestion/calendriers.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<?php if (!$calendrier): ?>
    <div class="alert alert-danger">Calendrier non trouvé.</div>
<?php else: ?>

<div class="row">
    <!-- Informations générales -->
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Libellé :</strong></td>
                        <td><?php echo htmlspecialchars($calendrier['libelle']); ?></td>
                    </tr>
                    <?php if (!empty($calendrier['description'])): ?>
                    <tr>
                        <td><strong>Description :</strong></td>
                        <td><?php echo nl2br(htmlspecialchars($calendrier['description'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Période :</strong></td>
                        <td>
                            Du <?php echo date('d/m/Y', strtotime($calendrier['date_debut'])); ?> 
                            au <?php echo date('d/m/Y', strtotime($calendrier['date_fin'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Statut :</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $calendrier['actif'] ? 'success' : 'secondary'; ?>">
                                <?php echo $calendrier['actif'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Nombre de tranches :</strong></td>
                        <td><span class="badge bg-info"><?php echo count($calendrier['tranches'] ?? []); ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Répartition des tranches -->
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition des tranches</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($calendrier['tranches'])): 
                    $totalPourcentage = 0;
                    foreach ($calendrier['tranches'] as $t) {
                        $totalPourcentage += $t['pourcentage'];
                    }
                ?>
                    <?php foreach ($calendrier['tranches'] as $index => $tranche): ?>
                    <div class="card mb-3 tranche-card" style="border-left: 4px solid <?php 
                        $colors = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#F44336', '#00BCD4'];
                        echo $colors[$index % count($colors)]; 
                    ?>;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                    <?php echo htmlspecialchars($tranche['libelle']); ?>
                                </h6>
                                <span class="badge bg-info"><?php echo $tranche['pourcentage']; ?>%</span>
                            </div>
                            
                            <div class="progress mt-2 mb-2" style="height: 20px;">
                                <div class="progress-bar" 
                                     style="width: <?php echo $tranche['pourcentage']; ?>%; background: <?php echo $colors[$index % count($colors)]; ?>;">
                                    <?php echo $tranche['pourcentage']; ?>%
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i> 
                                        Date limite : <strong><?php echo date('d/m/Y', strtotime($tranche['date_limite'])); ?></strong>
                                        <?php 
                                        $dateLimite = new DateTime($tranche['date_limite']);
                                        $aujourdhui = new DateTime();
                                        $interval = $aujourdhui->diff($dateLimite);
                                        
                                        if ($dateLimite < $aujourdhui): ?>
                                            <span class="badge bg-danger">Dépassée</span>
                                        <?php elseif ($interval->days <= 7): ?>
                                            <span class="badge bg-warning">Bientôt</span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($tranche['description'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($tranche['description']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Barre de progression totale -->
                    <div class="mt-3">
                        <label class="form-label"><strong>Progression totale :</strong></label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $totalPourcentage; ?>%;">
                                <?php echo $totalPourcentage; ?>%
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-layer-group fa-3x mb-3"></i>
                        <p>Aucune tranche définie pour ce calendrier.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Calendrier visuel -->
<?php if (!empty($calendrier['tranches'])): ?>
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Calendrier visuel</h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            <?php 
            $debut = new DateTime($calendrier['date_debut']);
            $fin = new DateTime($calendrier['date_fin']);
            $dureeTotale = $debut->diff($fin)->days;
            ?>
            
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-secondary me-2">Début</span>
                <small><?php echo date('d/m/Y', strtotime($calendrier['date_debut'])); ?></small>
                <div class="flex-grow-1 mx-2" style="height: 2px; background: #ddd;"></div>
                <span class="badge bg-secondary me-2">Fin</span>
                <small><?php echo date('d/m/Y', strtotime($calendrier['date_fin'])); ?></small>
            </div>
            
            <?php foreach ($calendrier['tranches'] as $index => $tranche): 
                $dateTranche = new DateTime($tranche['date_limite']);
                $position = $dureeTotale > 0 ? ($debut->diff($dateTranche)->days / $dureeTotale) * 100 : 0;
                $colors = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#F44336', '#00BCD4'];
                $color = $colors[$index % count($colors)];
            ?>
                <div class="position-relative mb-3" style="margin-left: <?php echo min($position, 95); ?>%;">
                    <div style="border-left: 2px solid <?php echo $color; ?>; padding-left: 10px;">
                        <span class="badge" style="background: <?php echo $color; ?>;">
                            <?php echo $index + 1; ?>
                        </span>
                        <strong><?php echo htmlspecialchars($tranche['libelle']); ?></strong>
                        <small class="text-muted ms-2"><?php echo date('d/m/Y', strtotime($tranche['date_limite'])); ?></small>
                        <span class="badge bg-info ms-1"><?php echo $tranche['pourcentage']; ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<style>
.tranche-card {
    transition: transform 0.2s;
}
.tranche-card:hover {
    transform: translateX(5px);
}
.timeline {
    position: relative;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>