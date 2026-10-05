<?php
$pageTitle = "Calendriers de Paiement";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-alt"></i> Calendriers de Paiement</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/calendrier_ajouter.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Calendrier
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row">
    <?php foreach ($calendriers as $cal): ?>
    <div class="col-md-6 mb-4">
        <div class="card <?php echo $cal['actif'] ? 'border-primary' : ''; ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check"></i> 
                    <?php echo htmlspecialchars($cal['libelle']); ?>
                </h5>
                <?php if ($cal['actif']): ?>
                    <span class="badge bg-success">Actif</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Année :</strong> <?php echo $cal['annee_libelle']; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Tranches :</strong> <?php echo $cal['nombre_tranches'] ?? 0; ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Début :</strong> <?php echo date('d/m/Y', strtotime($cal['date_debut'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Fin :</strong> <?php echo date('d/m/Y', strtotime($cal['date_fin'])); ?>
                    </div>
                </div>
                
                <?php if (!empty($cal['description'])): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($cal['description']); ?></p>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo SITE_URL; ?>/gestion/calendrier_details.php?id=<?php echo $cal['id']; ?>" 
                       class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                    <a href="<?php echo SITE_URL; ?>/gestion/calendrier_modifier.php?id=<?php echo $cal['id']; ?>" 
                       class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($calendriers)): ?>
    <div class="col-12">
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-calendar fa-3x mb-3"></i>
            <h5>Aucun calendrier de paiement</h5>
            <p>Créez votre premier calendrier pour définir les échéances de paiement.</p>
            <a href="<?php echo SITE_URL; ?>/gestion/calendrier_ajouter.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Calendrier
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>