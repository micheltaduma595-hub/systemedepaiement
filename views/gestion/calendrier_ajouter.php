<?php
$pageTitle = "Nouveau Calendrier de Paiement";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-plus"></i> Nouveau Calendrier de Paiement</h2>
    <a href="<?php echo SITE_URL; ?>/gestion/calendriers.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="POST" id="formCalendrier">
    <div class="row">
        <div class="col-md-6">
            <!-- Informations générales -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations du calendrier</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" name="libelle" class="form-control" required 
                               placeholder="Ex: Paiement en 3 tranches">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" 
                                  placeholder="Description du calendrier..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Année académique *</label>
                        <select name="annee_id" class="form-select" required>
                            <option value="">Choisir...</option>
                            <?php foreach ($annees as $a): ?>
                                <option value="<?php echo $a['id']; ?>" <?php echo $a['actif'] ? 'selected' : ''; ?>>
                                    <?php echo $a['libelle']; ?> <?php echo $a['actif'] ? '(Active)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date début *</label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date fin *</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Résumé -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Information</h5>
                </div>
                <div class="card-body">
                    <p>Définissez les tranches de paiement ci-dessous.</p>
                    <ul>
                        <li>Chaque tranche correspond à un pourcentage du montant total</li>
                        <li>Le total des pourcentages doit être égal à <strong>100%</strong></li>
                        <li>Les dates limites indiquent quand la tranche doit être payée</li>
                    </ul>
                    <div id="resumePourcentage" class="alert alert-warning" style="display:none;">
                        Total actuel : <strong><span id="totalPourcentage">0</span>%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tranches de paiement -->
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-layer-group"></i> Tranches de paiement</h5>
            <button type="button" class="btn btn-light btn-sm" onclick="ajouterTranche()">
                <i class="fas fa-plus"></i> Ajouter une tranche
            </button>
        </div>
        <div class="card-body">
            <div id="tranchesContainer">
                <!-- Les tranches seront ajoutées ici dynamiquement -->
            </div>
            <div id="messageTranches" class="text-center text-muted py-3">
                <i class="fas fa-info-circle"></i> Ajoutez au moins une tranche de paiement
            </div>
        </div>
    </div>
    
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Créer le calendrier
        </button>
    </div>
</form>

<script>
let compteurTranche = 0;

function ajouterTranche() {
    compteurTranche++;
    const container = document.getElementById('tranchesContainer');
    document.getElementById('messageTranches').style.display = 'none';
    
    const html = `
        <div class="tranche-item card mb-3" id="tranche_${compteurTranche}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <span class="badge bg-primary">Tranche ${compteurTranche}</span>
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="supprimerTranche(${compteurTranche})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Nom de la tranche *</label>
                        <input type="text" name="tranche_nom[]" class="form-control" required 
                               placeholder="Ex: 1ère tranche">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Pourcentage (%) *</label>
                        <input type="number" name="tranche_pourcentage[]" class="form-control tranche-pct" 
                               step="0.01" min="0" max="100" required 
                               onchange="calculerTotal()" onkeyup="calculerTotal()">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Date limite *</label>
                        <input type="date" name="tranche_date[]" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" name="tranche_description[]" class="form-control" 
                               placeholder="Ex: Premier versement">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    calculerTotal();
}

function supprimerTranche(id) {
    document.getElementById('tranche_' + id).remove();
    calculerTotal();
    
    // Réorganiser les numéros
    const tranches = document.querySelectorAll('.tranche-item');
    tranches.forEach((tranche, index) => {
        tranche.querySelector('.badge').textContent = 'Tranche ' + (index + 1);
    });
}

function calculerTotal() {
    const pcts = document.querySelectorAll('.tranche-pct');
    let total = 0;
    
    pcts.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    const resumeDiv = document.getElementById('resumePourcentage');
    const totalSpan = document.getElementById('totalPourcentage');
    
    totalSpan.textContent = total.toFixed(2);
    resumeDiv.style.display = pcts.length > 0 ? 'block' : 'none';
    
    // Changer la couleur selon le total
    if (Math.abs(total - 100) < 0.01) {
        resumeDiv.className = 'alert alert-success';
    } else if (total > 100) {
        resumeDiv.className = 'alert alert-danger';
    } else {
        resumeDiv.className = 'alert alert-warning';
    }
}

// Ajouter automatiquement 3 tranches par défaut
window.onload = function() {
    ajouterTranche();
    ajouterTranche();
    ajouterTranche();
};
</script>

<style>
.tranche-item {
    border-left: 3px solid #0d6efd;
    background: #f8f9fa;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>