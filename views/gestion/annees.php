<?php
$pageTitle = "Années Académiques";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar"></i> Années Académiques</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/annee_ajouter.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Année
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($annees as $annee): ?>
    <div class="col-md-4 mb-3">
        <div class="card <?php echo $annee['actif'] ? 'border-primary' : ''; ?>">
            <div class="card-header <?php echo $annee['actif'] ? 'bg-primary text-white' : ''; ?>">
                <h5 class="mb-0">
                    <?php echo $annee['libelle']; ?>
                    <?php if ($annee['actif']): ?>
                        <span class="badge bg-success float-end">Active</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Début :</strong> <?php echo date('d/m/Y', strtotime($annee['date_debut'])); ?></p>
                <p><strong>Fin :</strong> <?php echo date('d/m/Y', strtotime($annee['date_fin'])); ?></p>
                
                <div class="d-flex justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/gestion/annee_details.php?id=<?php echo $annee['id']; ?>" 
                       class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                    
                    <?php if (!$annee['actif']): ?>
                    <a href="<?php echo SITE_URL; ?>/gestion/annee_activer.php?id=<?php echo $annee['id']; ?>" 
                       class="btn btn-sm btn-success"
                       onclick="return confirm('Activer cette année ? Les autres seront désactivées.')">
                        <i class="fas fa-check"></i> Activer
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>