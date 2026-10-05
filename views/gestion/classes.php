<?php
$pageTitle = "Gestion des Classes";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-school"></i> Classes</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/classe_ajouter.php" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nouvelle Classe
</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Classe</th>
                    <th>Niveau</th>
                    <th>Option</th>
                    <th>Élèves</th>
                    <th>Capacité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $classe): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($classe['nom']); ?></strong></td>
                    <td><?php echo $classe['niveau']; ?></td>
                    <td><?php echo $classe['option_nom']; ?></td>
                    <td><?php echo $classe['nombre_eleves']; ?></td>
                    <td><?php echo $classe['capacite']; ?></td>
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