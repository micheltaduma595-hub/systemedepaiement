<?php
$pageTitle = "Ajouter une Année Académique";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouvelle Année Académique</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/annees.php" class="btn btn-secondary">
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
                               placeholder="Ex: 2025-2026">
                        <small class="text-muted">Format recommandé : AAAA-AAAA</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début *</label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin *</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="actif" class="form-check-input" id="actif">
                        <label class="form-check-label" for="actif">Définir comme année active</label>
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