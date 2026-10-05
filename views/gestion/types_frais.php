<?php
$pageTitle = "Types de Frais";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags"></i> Types de Frais</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/type_frais_ajouter.php" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nouveau Type
</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr><th>ID</th><th>Libellé</th><th>Description</th></tr>
            </thead>
            <tbody>
                <?php foreach ($types as $t): ?>
                <tr>
                    <td><?php echo $t['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($t['libelle']); ?></strong></td>
                    <td><?php echo htmlspecialchars($t['description'] ?? '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>