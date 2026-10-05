<?php
$pageTitle = "Ajouter Type de Frais";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouveau Type de Frais</h2>
  <a href="<?php echo SITE_URL; ?>/gestion/types_frais.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour
</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" name="libelle" class="form-control" required 
                               placeholder="Ex: Minerval, Transport...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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