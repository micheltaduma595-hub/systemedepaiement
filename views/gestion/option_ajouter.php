<?php
$pageTitle = "Ajouter une Option";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouvelle Option</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/options.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour
</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nom de l'option *</label>
                        <input type="text" name="nom" class="form-control" required 
                               placeholder="Ex: Scientifique, Littéraire...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Description de l'option..."></textarea>
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