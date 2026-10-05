<?php
$pageTitle = "Ajouter un Utilisateur";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus"></i> Nouvel Utilisateur</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/utilisateurs.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe *</label>
                            <input type="password" name="mot_de_passe" class="form-control" 
                                   required minlength="6"
                                   placeholder="Minimum 6 caractères">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rôles *</label>
                        <div class="row">
                            <?php foreach ($roles as $role): 
                                $icon = 'fa-user';
                                $color = 'secondary';
                                if ($role['nom'] == 'admin') { $icon = 'fa-crown'; $color = 'danger'; }
                                elseif ($role['nom'] == 'caissier') { $icon = 'fa-money-bill'; $color = 'success'; }
                                elseif ($role['nom'] == 'secretaire') { $icon = 'fa-clipboard'; $color = 'info'; }
                                elseif ($role['nom'] == 'comptable') { $icon = 'fa-calculator'; $color = 'warning'; }
                            ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="roles[]" value="<?php echo $role['id']; ?>" 
                                           id="role_<?php echo $role['id']; ?>">
                                    <label class="form-check-label" for="role_<?php echo $role['id']; ?>">
                                        <i class="fas <?php echo $icon; ?> text-<?php echo $color; ?>"></i>
                                        <?php echo ucfirst($role['nom']); ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l'utilisateur
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Rôles disponibles</h6>
            </div>
            <div class="card-body">
                <?php foreach ($roles as $role): ?>
                <div class="mb-3">
                    <strong><?php echo ucfirst($role['nom']); ?></strong>
                    <p class="text-muted small"><?php echo $role['description'] ?? 'Aucune description'; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>