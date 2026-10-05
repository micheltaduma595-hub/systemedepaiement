<?php
$pageTitle = "Accès Refusé";
ob_start();
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-lg">
                <div class="card-body py-5">
                    <i class="fas fa-lock fa-5x text-danger mb-4"></i>
                    <h2 class="text-danger">Accès Refusé</h2>
                    <p class="text-muted">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
                    <p class="text-muted small">Contactez l'administrateur si vous pensez que c'est une erreur.</p>
                    <a href="<?php echo SITE_URL; ?>/dashboard.php" class="btn btn-primary-custom mt-3">
                        <i class="fas fa-home me-2"></i> Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>