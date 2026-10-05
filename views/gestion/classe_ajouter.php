<?php
$pageTitle = "Ajouter une Classe";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouvelle Classe</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/classes.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour
</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nom de la classe *</label>
                        <input type="text" name="nom" class="form-control" required 
                               placeholder="Ex: 6ème A, 5ème B...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Niveau *</label>
                        <select name="niveau" class="form-select" required>
                            <option value="">Choisir...</option>
                            <option value="7ème">7ème</option>
                            <option value="8ème">8ème</option>
                            <option value="9ème">9ème</option>
                            <option value="10ème">10ème</option>
                            <option value="11ème">11ème</option>
                            <option value="12ème">12ème</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Option *</label>
                        <select name="option_id" class="form-select" required>
                            <option value="">Choisir...</option>
                            <?php foreach ($options as $opt): ?>
                                <option value="<?php echo $opt['id']; ?>"><?php echo $opt['nom']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacité</label>
                        <input type="number" name="capacite" class="form-control" value="50">
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