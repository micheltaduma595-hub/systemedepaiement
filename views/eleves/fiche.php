<?php
$pageTitle = "Fiche Élève - " . $eleve['nom'] . ' ' . $eleve['prenom'];
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-id-card"></i> Fiche Élève</h2>
    <div>
        <a href="../eleves.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>
</div>

<div class="row">
    <!-- Informations personnelles -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informations personnelles</h5>
            </div>
            <div class="card-body text-center">
                <img src="<?php echo $eleve['photo'] ?? '../assets/images/default-avatar.png'; ?>" 
                     class="rounded-circle mb-3" width="100" height="100">
                <h4><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></h4>
                <span class="badge bg-secondary"><?php echo $eleve['matricule']; ?></span>
                
                <hr>
                
                <ul class="list-unstyled text-start">
                    <li class="mb-2"><strong>Genre :</strong> <?php echo $eleve['genre'] == 'M' ? 'Masculin' : 'Féminin'; ?></li>
                    <li class="mb-2"><strong>Date de naissance :</strong> <?php echo $eleve['date_naissance'] ? date('d/m/Y', strtotime($eleve['date_naissance'])) : '-'; ?></li>
                    <li class="mb-2"><strong>Téléphone :</strong> <?php echo $eleve['telephone'] ?? '-'; ?></li>
                    <li class="mb-2"><strong>Email :</strong> <?php echo $eleve['email'] ?? '-'; ?></li>
                    <li class="mb-2"><strong>Adresse :</strong> <?php echo $eleve['adresse'] ?? '-'; ?></li>
                </ul>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Parent/Tuteur</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Nom :</strong> <?php echo $eleve['nom_parent'] ?? '-'; ?></li>
                    <li class="mb-2"><strong>Téléphone :</strong> <?php echo $eleve['telephone_parent'] ?? '-'; ?></li>
                    <li class="mb-2"><strong>Email :</strong> <?php echo $eleve['email_parent'] ?? '-'; ?></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Informations scolaires et paiements -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Informations scolaires</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Classe :</strong> <?php echo $eleve['classe_nom'] ?? 'Non inscrit'; ?></p>
                        <p><strong>Niveau :</strong> <?php echo $eleve['niveau'] ?? '-'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Option :</strong> <?php echo $eleve['option_nom'] ?? '-'; ?></p>
                        <p><strong>Année :</strong> <?php echo $eleve['annee_academique'] ?? '-'; ?></p>
                    </div>
                </div>
                <p><strong>Date d'inscription :</strong> <?php echo isset($eleve['date_inscription']) ? date('d/m/Y', strtotime($eleve['date_inscription'])) : '-'; ?></p>
                <p><strong>Statut :</strong> 
                    <span class="badge bg-<?php echo ($eleve['statut_inscription'] ?? '') == 'actif' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($eleve['statut_inscription'] ?? 'Non inscrit'); ?>
                    </span>
                </p>
            </div>
        </div>
        
        <!-- Frais et paiements -->
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">État des paiements</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Frais</th>
                                <th>Montant dû</th>
                                <th>Montant payé</th>
                                <th>Solde</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalDu = 0;
                            $totalPaye = 0;
                            foreach ($frais as $frais):
                                // Calculer le payé pour ce frais
                                $paye = 0;
                                foreach ($paiements as $p) {
                                    if ($p['frais_id'] == $frais['id']) {
                                        $paye += $p['montant_paye'];
                                    }
                                }
                                $solde = $frais['montant'] - $paye;
                                $totalDu += $frais['montant'];
                                $totalPaye += $paye;
                            ?>
                            <tr>
                                <td><?php echo $frais['libelle']; ?></td>
                                <td class="text-end"><?php echo number_format($frais['montant'], 2); ?> FC</td>
                                <td class="text-end"><?php echo number_format($paye, 2); ?> FC</td>
                                <td class="text-end <?php echo $solde > 0 ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo number_format($solde, 2); ?> FC
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $solde <= 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $solde <= 0 ? 'Payé' : 'En attente'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end"><?php echo number_format($totalDu, 2); ?> FC</th>
                                <th class="text-end"><?php echo number_format($totalPaye, 2); ?> FC</th>
                                <th class="text-end"><?php echo number_format($totalDu - $totalPaye, 2); ?> FC</th>
                                <th>
                                    <?php 
                                    $taux = $totalDu > 0 ? ($totalPaye / $totalDu) * 100 : 0;
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-<?php echo $taux >= 100 ? 'success' : ($taux >= 50 ? 'warning' : 'danger'); ?>" 
                                             style="width: <?php echo min($taux, 100); ?>%">
                                            <?php echo round($taux, 1); ?>%
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Historique des paiements -->
                <?php if (!empty($paiements)): ?>
                <h6 class="mt-4">Historique des paiements</h6>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Frais</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Reçu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiements as $p): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['date_paiement'])); ?></td>
                            <td><?php echo $p['frais_libelle'] ?? ''; ?></td>
                            <td class="text-end"><?php echo number_format($p['montant_paye'], 2); ?> FC</td>
                            <td><?php echo ucfirst($p['mode_paiement']); ?></td>
                            <td>
                                <a href="../rapports/recu.php?id=<?php echo $p['id']; ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-receipt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>