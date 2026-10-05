<?php
$pageTitle = "Modifier l'élève - " . ($eleve['nom'] ?? '') . ' ' . ($eleve['prenom'] ?? '');
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit"></i> Modifier l'élève</h2>
    <a href="../eleves.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (!isset($eleve) || !$eleve): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> Élève non trouvé.
        <a href="../eleves.php" class="alert-link">Retourner à la liste</a>
    </div>
<?php else: ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="row">
                <!-- Informations de l'élève -->
                <div class="col-md-6">
                    <h4 class="mb-3">Informations de l'élève</h4>
                    
                    <div class="mb-3">
                        <label class="form-label">Matricule</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($eleve['matricule'] ?? ''); ?>" readonly>
                        <small class="text-muted">Le matricule ne peut pas être modifié</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required 
                                   value="<?php echo htmlspecialchars($eleve['nom'] ?? ''); ?>"
                                   placeholder="NOM en majuscules">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Post-nom</label>
                            <input type="text" name="postnom" class="form-control" 
                                   value="<?php echo htmlspecialchars($eleve['postnom'] ?? ''); ?>"
                                   placeholder="Post-nom">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required 
                               value="<?php echo htmlspecialchars($eleve['prenom'] ?? ''); ?>"
                               placeholder="Prénom">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Genre *</label>
                            <select name="genre" class="form-select" required>
                                <option value="">Choisir...</option>
                                <option value="M" <?php echo (isset($eleve['genre']) && $eleve['genre'] == 'M') ? 'selected' : ''; ?>>Masculin</option>
                                <option value="F" <?php echo (isset($eleve['genre']) && $eleve['genre'] == 'F') ? 'selected' : ''; ?>>Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" 
                                   value="<?php echo $eleve['date_naissance'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($eleve['email'] ?? ''); ?>"
                               placeholder="email@exemple.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" 
                               value="<?php echo htmlspecialchars($eleve['telephone'] ?? ''); ?>"
                               placeholder="+243...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($eleve['adresse'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Informations du parent -->
                <div class="col-md-6">
                    <h4 class="mb-3">Informations du parent/tuteur</h4>
                    
                    <div class="mb-3">
                        <label class="form-label">Nom du parent</label>
                        <input type="text" name="nom_parent" class="form-control" 
                               value="<?php echo htmlspecialchars($eleve['nom_parent'] ?? ''); ?>"
                               placeholder="Nom complet du parent">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone du parent</label>
                        <input type="text" name="telephone_parent" class="form-control" 
                               value="<?php echo htmlspecialchars($eleve['telephone_parent'] ?? ''); ?>"
                               placeholder="+243...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email du parent</label>
                        <input type="email" name="email_parent" class="form-control" 
                               value="<?php echo htmlspecialchars($eleve['email_parent'] ?? ''); ?>"
                               placeholder="parent@exemple.com">
                    </div>
                    
                    <!-- Résumé -->
                    <h4 class="mb-3 mt-4">Résumé</h4>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p><strong>Matricule :</strong> <?php echo htmlspecialchars($eleve['matricule'] ?? ''); ?></p>
                            <p><strong>Statut :</strong> 
                                <?php if (isset($eleve['actif']) && $eleve['actif']): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Date de création :</strong> <?php echo isset($eleve['created_at']) ? date('d/m/Y', strtotime($eleve['created_at'])) : 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <a href="../eleves.php" class="btn btn-secondary btn-lg">Annuler</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<script>
// Validation du formulaire
(function() {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
$content = ob_get_clean();
// CORRECTION DU CHEMIN : Remonter d'un niveau (eleves) puis aller dans views
require_once __DIR__ . '/../views/template.php';
?>