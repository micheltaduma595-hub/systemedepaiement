<?php
$pageTitle = "Modifier l'utilisateur - " . ($utilisateur['nom'] ?? '');
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit"></i> Modifier l'utilisateur</h2>
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
                            <input type="text" name="nom" class="form-control" required 
                                   value="<?php echo htmlspecialchars($utilisateur['nom'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo $utilisateur['email'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" 
                                   placeholder="Laisser vide pour ne pas changer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" 
                                   value="<?php echo $utilisateur['telephone'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="actif" class="form-check-input" id="actif" 
                               <?php echo (isset($utilisateur['actif']) && $utilisateur['actif']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="actif">Compte actif</label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rôles *</label>
                        <div class="row">
                            <?php 
                            $userRoles = explode(',', $utilisateur['role_ids'] ?? '');
                            foreach ($roles as $role): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="roles[]" value="<?php echo $role['id']; ?>" 
                                           id="role_<?php echo $role['id']; ?>"
                                           <?php echo in_array($role['id'], $userRoles) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="role_<?php echo $role['id']; ?>">
                                        <?php echo ucfirst($role['nom']); ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>