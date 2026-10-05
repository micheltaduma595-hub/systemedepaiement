<?php
$pageTitle = "Nouveau Paiement";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle"></i> Nouveau Paiement</h2>
    <a href="<?php echo SITE_URL; ?>/paiements.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Enregistrer un paiement</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo SITE_URL; ?>/controllers/paiement_store.php">
                    
                    <!-- Étape 1 : Choisir l'élève -->
                    <div class="mb-3">
                        <label class="form-label">1. Choisir l'élève *</label>
                        <select name="inscription_id" id="selectEleve" class="form-select" required>
                            <option value="">Sélectionner un élève...</option>
                            <?php foreach ($eleves as $e): ?>
                                <option value="<?php echo $e['inscription_id']; ?>"
                                        data-classe-id="<?php echo $e['classe_id'] ?? ''; ?>">
                                    <?php echo $e['matricule'] . ' - ' . $e['nom'] . ' ' . $e['prenom'] . ' (' . $e['classe_nom'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Info élève -->
                    <div id="infoEleve" style="display:none; background:#e3f2fd; padding:15px; border-radius:5px; margin-bottom:15px;">
                        <strong>Élève :</strong> <span id="infoNom"></span> | 
                        <strong>Classe :</strong> <span id="infoClasse"></span> | 
                        <strong>Matricule :</strong> <span id="infoMatricule"></span>
                        <br>
                        <strong>Total frais :</strong> <span id="infoTotal"></span> | 
                        <strong>Payé :</strong> <span id="infoPaye" style="color:green;"></span> | 
                        <strong>Solde :</strong> <span id="infoSolde" style="color:red; font-weight:bold;"></span>
                    </div>
                    
                    <!-- Étape 2 : Choisir le frais -->
                    <div class="mb-3">
                        <label class="form-label">2. Type de frais *</label>
                        <select name="frais_id" id="selectFrais" class="form-select" required disabled>
                            <option value="">Veuillez d'abord sélectionner un élève</option>
                        </select>
                    </div>
                    
                    <!-- Détail frais -->
                    <div id="detailFrais" style="display:none; background:#fff3e0; padding:10px; border-radius:5px; margin-bottom:15px;">
                        <span id="detailInfo"></span>
                    </div>
                    
                    <!-- Étape 3 : Montant et mode -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">3. Montant à payer (FC) *</label>
                            <input type="number" name="montant_paye" id="montantPaye" 
                                   class="form-control" step="0.01" min="1" required
                                   placeholder="0.00">
                            <small>Maximum : <strong><span id="montantMax">0</span> FC</strong></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">4. Mode de paiement *</label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="">Choisir...</option>
                                <option value="espece">Espèce</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="virement">Virement bancaire</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Référence</label>
                        <input type="text" name="reference" class="form-control" placeholder="N° transaction">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle"></i> Valider le paiement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Attendre que la page soit chargée
document.addEventListener('DOMContentLoaded', function() {
    
    // Quand on change l'élève
    document.getElementById('selectEleve').addEventListener('change', function() {
        var inscriptionId = this.value;
        var classeId = this.options[this.selectedIndex].getAttribute('data-classe-id');
        
        if (!inscriptionId) {
            document.getElementById('infoEleve').style.display = 'none';
            document.getElementById('selectFrais').innerHTML = '<option value="">Veuillez d\'abord sélectionner un élève</option>';
            document.getElementById('selectFrais').disabled = true;
            return;
        }
        
        // Appeler l'AJAX pour les infos élève
        var xhr1 = new XMLHttpRequest();
        xhr1.open('GET', '<?php echo SITE_URL; ?>/controllers/get_eleve_info.php?inscription_id=' + inscriptionId, true);
        xhr1.onload = function() {
            if (xhr1.status === 200) {
                var data = JSON.parse(xhr1.responseText);
                if (data.success) {
                    document.getElementById('infoNom').textContent = data.eleve.nom_complet;
                    document.getElementById('infoClasse').textContent = data.eleve.classe;
                    document.getElementById('infoMatricule').textContent = data.eleve.matricule;
                    document.getElementById('infoTotal').textContent = data.total_frais + ' FC';
                    document.getElementById('infoPaye').textContent = data.total_paye + ' FC';
                    document.getElementById('infoSolde').textContent = data.solde + ' FC';
                    document.getElementById('infoEleve').style.display = 'block';
                    
                    // Charger les frais
                    chargerFrais(classeId, inscriptionId);
                }
            }
        };
        xhr1.send();
    });
    
    // Fonction charger les frais
    function chargerFrais(classeId, inscriptionId) {
        var xhr2 = new XMLHttpRequest();
        xhr2.open('GET', '<?php echo SITE_URL; ?>/controllers/get_frais_by_classe.php?classe_id=' + classeId + '&inscription_id=' + inscriptionId, true);
        xhr2.onload = function() {
            if (xhr2.status === 200) {
                var data = JSON.parse(xhr2.responseText);
                if (data.success) {
                    var select = document.getElementById('selectFrais');
                    select.innerHTML = '<option value="">Sélectionner le frais...</option>';
                    select.disabled = false;
                    
                    data.frais.forEach(function(f) {
                        var opt = document.createElement('option');
                        opt.value = f.id;
                        opt.textContent = f.type_frais_libelle + ' - ' + f.libelle + ' (Reste: ' + f.reste_a_payer + ' FC)';
                        if (f.est_paye) {
                            opt.disabled = true;
                            opt.textContent += ' [PAYÉ]';
                        }
                        opt.setAttribute('data-montant', f.reste_a_payer);
                        opt.setAttribute('data-type', f.type_frais_libelle);
                        opt.setAttribute('data-semestre', f.semestre_nom);
                        select.appendChild(opt);
                    });
                }
            }
        };
        xhr2.send();
    }
    
    // Quand on change le frais
    document.getElementById('selectFrais').addEventListener('change', function() {
        var option = this.options[this.selectedIndex];
        var montant = option.getAttribute('data-montant');
        var type = option.getAttribute('data-type');
        var semestre = option.getAttribute('data-semestre');
        
        if (montant) {
            document.getElementById('detailFrais').style.display = 'block';
            document.getElementById('detailInfo').textContent = 
                'Type: ' + type + ' | Semestre: ' + semestre + ' | À payer: ' + parseFloat(montant).toLocaleString('fr-FR') + ' FC';
            document.getElementById('montantMax').textContent = parseFloat(montant).toLocaleString('fr-FR');
            document.getElementById('montantPaye').value = montant;
            document.getElementById('montantPaye').max = montant;
        }
    });
    
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>