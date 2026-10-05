<?php
$pageTitle = "Ajouter un Élève";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus"></i> Nouvel Élève</h2>
    <a href="../eleves.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="../controllers/eleve_store.php" class="needs-validation" novalidate>
            <div class="row">
                <!-- Informations de l'élève -->
                <div class="col-md-6">
                    <h4 class="mb-3">Informations de l'élève</h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required 
                                   placeholder="NOM en majuscules">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Post-nom</label>
                            <input type="text" name="postnom" class="form-control" 
                                   placeholder="Post-nom">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required 
                               placeholder="Prénom">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Genre *</label>
                            <select name="genre" class="form-select" required>
                                <option value="">Choisir...</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@exemple.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" placeholder="+243...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea name="adresse" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <!-- Informations du parent -->
                <div class="col-md-6">
                    <h4 class="mb-3">Informations du parent/tuteur</h4>
                    
                    <div class="mb-3">
                        <label class="form-label">Nom du parent</label>
                        <input type="text" name="nom_parent" class="form-control" 
                               placeholder="Nom complet du parent">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone du parent</label>
                        <input type="text" name="telephone_parent" class="form-control" 
                               placeholder="+243...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email du parent</label>
                        <input type="email" name="email_parent" class="form-control" 
                               placeholder="parent@exemple.com">
                    </div>
                    
                    <h4 class="mb-3 mt-4">Inscription</h4>
                    
                    <div class="mb-3">
                        <label class="form-label">Classe *</label>
                        <select name="classe_id" class="form-select">
                            <option value="">Sélectionner une classe...</option>
                            <?php foreach ($classes as $classe): ?>
                                <option value="<?php echo $classe['id']; ?>">
                                    <?php echo $classe['nom'] . ' (' . $classe['niveau'] . ') - ' . $classe['option_nom']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Année académique</label>
                        <select name="annee_id" class="form-select">
                            <?php foreach ($annees as $annee): ?>
                                <option value="<?php echo $annee['id']; ?>" 
                                        <?php echo $annee['actif'] ? 'selected' : ''; ?>>
                                    <?php echo $annee['libelle']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <a href="../eleves.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

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
require_once __DIR__ . '/../template.php';
?>