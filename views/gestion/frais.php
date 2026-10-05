<?php
$pageTitle = "Gestion des Frais";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice-dollar"></i> Frais Scolaires</h2>
   <a href="<?php echo SITE_URL; ?>/gestion/frais_ajouter.php" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nouveau Frais
</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Libellé</th>
                    <th>Type</th>
                    <th>Classe</th>
                    <th>Semestre</th>
                    <th>Échéance</th>
                    <th>Date limite</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($frais as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f['libelle']); ?></td>
                    <td><?php echo $f['type_frais_libelle']; ?></td>
                    <td><?php echo $f['classe_nom'] . ' (' . $f['niveau'] . ')'; ?></td>
                    <td><?php echo $f['semestre_nom']; ?></td>
                    <td><?php echo $f['echeance_libelle'] ?? '-'; ?></td>
                    <td><?php echo $f['date_limite'] ? date('d/m/Y', strtotime($f['date_limite'])) : '-'; ?></td>
                    <td class="text-end fw-bold"><?php echo number_format($f['montant'], 2); ?> FC</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>