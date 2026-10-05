<?php
$pageTitle = "Rapport Journalier - " . date('d/m/Y', strtotime($date));
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar"></i> Rapport Journalier</h2>
    <div>
        <button onclick="window.print()" class="btn btn-info me-2 no-print">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="../paiements.php" class="btn btn-secondary no-print">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Sélection de la date -->
<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Date du rapport</label>
                <input type="date" name="date" class="form-control" value="<?php echo $date; ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Afficher
                </button>
                <a href="?date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary">
                    Aujourd'hui
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Résumé -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Total du jour</h6>
                <h3><?php echo number_format($totalJour, 0); ?> FC</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6>Transactions</h6>
                <h3><?php echo count($paiements); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6>Par mode de paiement</h6>
                <table class="table table-sm mb-0">
                    <?php foreach ($resume as $r): ?>
                    <tr>
                        <td><?php echo ucfirst(str_replace('_', ' ', $r['mode_paiement'])); ?></td>
                        <td class="text-end"><?php echo $r['nombre']; ?> transaction(s)</td>
                        <td class="text-end fw-bold"><?php echo number_format($r['total'], 2); ?> FC</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Liste des transactions -->
<div class="card">
    <div class="card-header">
        <h5>Transactions du <?php echo date('d/m/Y', strtotime($date)); ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Heure</th>
                        <th>Reçu</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Frais</th>
                        <th>Montant</th>
                        <th>Mode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($paiements as $p): 
                        $total += $p['montant_paye'];
                    ?>
                    <tr>
                        <td><?php echo date('H:i', strtotime($p['date_paiement'])); ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo $p['recu_numero'] ?? $p['id']; ?>
                            </span>
                        </td>
                        <td><?php echo $p['nom'] . ' ' . $p['prenom']; ?></td>
                        <td><?php echo $p['classe_nom']; ?></td>
                        <td><?php echo $p['frais_libelle']; ?></td>
                        <td class="text-end fw-bold">
                            <?php echo number_format($p['montant_paye'], 2); ?> FC
                        </td>
                        <td><?php echo ucfirst($p['mode_paiement']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="5" class="text-end">TOTAL</th>
                        <th class="text-end"><?php echo number_format($total, 2); ?> FC</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php if (empty($paiements)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucune transaction pour cette date</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>