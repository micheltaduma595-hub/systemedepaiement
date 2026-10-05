<?php
$pageTitle = "Gestion des Paiements";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-money-bill-wave"></i> Paiements</h2>
    <div>
        <a href="paiements/rapport_journalier.php" class="btn btn-info me-2">
            <i class="fas fa-chart-bar"></i> Rapport journalier
        </a>
        <a href="paiements/nouveau.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Paiement
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Cartes statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total des paiements</h6>
                <h3><?php echo number_format($stats['total_paiements'], 0); ?> FC</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Paiements du jour</h6>
                <h3><?php echo number_format($stats['paiements_jour'], 0); ?> FC</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Transactions</h6>
                <h3><?php echo $stats['nombre_transactions']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6>En attente</h6>
                <h3>0</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des paiements -->
<div class="card">
    <div class="card-header">
        <h5>Historique des paiements</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tablePaiements">
                <thead class="table-dark">
                    <tr>
                        <th>N° Reçu</th>
                        <th>Date</th>
                        <th>Matricule</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Frais</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Caissier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paiements as $p): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo $p['recu_numero'] ?? 'N/A'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($p['date_paiement'])); ?></td>
                        <td><?php echo $p['matricule']; ?></td>
                        <td>
                            <strong><?php echo $p['nom']; ?></strong>
                            <?php echo $p['prenom']; ?>
                        </td>
                        <td><?php echo $p['classe_nom']; ?></td>
                        <td><?php echo $p['frais_libelle']; ?></td>
                        <td class="text-end fw-bold">
                            <?php echo number_format($p['montant_paye'], 2); ?> FC
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <?php echo ucfirst($p['mode_paiement']); ?>
                            </span>
                        </td>
                        <td><?php echo $p['caissier_nom'] ?? '-'; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="paiements/recu.php?id=<?php echo $p['id']; ?>" 
                                   class="btn btn-outline-info" target="_blank" title="Voir le reçu">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                <?php if ($p['statut'] == 'valide'): ?>
                                <button onclick="annulerPaiement(<?php echo $p['id']; ?>)" 
                                        class="btn btn-outline-danger" title="Annuler">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal annulation -->
<div class="modal fade" id="modalAnnulation" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formAnnulation">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Annuler un paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir annuler ce paiement ?</p>
                    <div class="mb-3">
                        <label class="form-label">Motif d'annulation</label>
                        <textarea name="motif" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablePaiements').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        order: [[1, 'desc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});

function annulerPaiement(id) {
    $('#formAnnulation').attr('action', 'paiements/annuler.php?id=' + id);
    $('#modalAnnulation').modal('show');
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>