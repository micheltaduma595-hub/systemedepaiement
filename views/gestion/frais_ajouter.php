<?php
$pageTitle = "Ajouter un Frais";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouveau Frais</h2>
   <a href="<?php echo SITE_URL; ?>/gestion/frais.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour
</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de frais *</label>
                            <select name="type_frais_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($typesFrais as $tf): ?>
                                    <option value="<?php echo $tf['id']; ?>"><?php echo $tf['libelle']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Classe *</label>
                            <select name="classe_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?php echo $classe['id']; ?>">
                                        <?php echo $classe['nom'] . ' (' . $classe['niveau'] . ') - ' . $classe['option_nom']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semestre *</label>
                            <select name="semestre_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                <?php foreach ($semestres as $sem): ?>
                                    <option value="<?php echo $sem['id']; ?>">
                                        <?php echo $sem['nom'] . ' (' . $sem['annee'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Calendrier/Échéance</label>
                            <select name="calendrier_id" class="form-select">
                                <option value="">Aucun</option>
                                <?php foreach ($calendriers as $cal): ?>
                                    <option value="<?php echo $cal['id']; ?>">
                                        <?php echo $cal['libelle'] . ' (du ' . date('d/m/Y', strtotime($cal['date_debut'])) . ' au ' . date('d/m/Y', strtotime($cal['date_fin'])) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Libellé du frais *</label>
                        <input type="text" name="libelle" class="form-control" required 
                               placeholder="Ex: Minerval 1ère tranche">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant (FC) *</label>
                            <input type="number" name="montant" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date limite</label>
                            <input type="date" name="date_limite" class="form-control">
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