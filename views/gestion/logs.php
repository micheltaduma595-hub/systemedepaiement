<?php
$pageTitle = "Journaux d'activité";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history"></i> Journaux d'activité</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/utilisateurs.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                        <td><?php echo $log['utilisateur_nom'] ?? 'Système'; ?></td>
                        <td>
                            <span class="badge bg-info"><?php echo $log['action']; ?></span>
                        </td>
                        <td><?php echo $log['description'] ?? '-'; ?></td>
                        <td><small><?php echo $log['ip_address'] ?? '-'; ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>