<?php
$pageTitle = "Gestion des Options";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-layer-group"></i> Options</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/option_ajouter.php" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nouvelle Option
</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="row">
    <?php foreach ($options as $option): ?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($option['nom']); ?></h5>
                <p class="text-muted"><?php echo $option['nombre_classes']; ?> classe(s)</p>
                <?php if (!empty($option['description'])): ?>
                    <p><small><?php echo htmlspecialchars($option['description']); ?></small></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>