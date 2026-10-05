<?php
$pageTitle = "Gestion des Utilisateurs";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users-cog"></i> Utilisateurs</h2>
    <div>
        <a href="<?php echo SITE_URL; ?>/gestion/logs.php" class="btn btn-info me-2">
            <i class="fas fa-history"></i> Journaux
        </a>
        <a href="<?php echo SITE_URL; ?>/gestion/utilisateur_ajouter.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel Utilisateur
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôles</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($user['nom']); ?></strong>
                        </td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['telephone'] ?? '-'; ?></td>
                        <td>
                            <?php 
                            $roles = explode(', ', $user['roles_noms'] ?? '');
                            foreach ($roles as $role): 
                                $badgeColor = 'secondary';
                                if ($role == 'admin') $badgeColor = 'danger';
                                elseif ($role == 'caissier') $badgeColor = 'success';
                                elseif ($role == 'secretaire') $badgeColor = 'info';
                                elseif ($role == 'comptable') $badgeColor = 'warning';
                            ?>
                                <span class="badge bg-<?php echo $badgeColor; ?> me-1">
                                    <?php echo ucfirst($role); ?>
                                </span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['actif'] ? 'success' : 'danger'; ?>">
                                <?php echo $user['actif'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $user['derniere_connexion'] ? date('d/m/Y H:i', strtotime($user['derniere_connexion'])) : 'Jamais'; ?>
                        </td>
                        <td>
                            <a href="<?php echo SITE_URL; ?>/gestion/utilisateur_modifier.php?id=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
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