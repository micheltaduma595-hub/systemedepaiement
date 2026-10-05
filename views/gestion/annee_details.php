<?php
$pageTitle = "Détails - " . ($annee['libelle'] ?? 'Année Académique');
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-check"></i> Détails de l'année : <?php echo $annee['libelle'] ?? ''; ?></h2>
    <a href="<?php echo SITE_URL; ?>/gestion/annees.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (!$annee): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> Année académique non trouvée.
    </div>
<?php else: ?>

<!-- Cartes statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-2x mb-2"></i>
                <h5>Élèves inscrits</h5>
                <h3><?php echo $annee['nombre_eleves'] ?? 0; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <h5>Inscriptions</h5>
                <h3><?php echo $annee['nombre_inscriptions'] ?? 0; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                <h5>Total Paiements</h5>
                <h3><?php echo number_format($annee['total_paiements'] ?? 0, 0); ?> FC</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-<?php echo ($annee['actif'] ?? 0) ? 'success' : 'secondary'; ?> text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h5>Statut</h5>
                <h3><?php echo ($annee['actif'] ?? 0) ? 'ACTIVE' : 'Inactive'; ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Informations détaillées -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations générales</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Libellé :</strong></td>
                        <td><?php echo $annee['libelle']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date de début :</strong></td>
                        <td><?php echo date('d/m/Y', strtotime($annee['date_debut'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date de fin :</strong></td>
                        <td><?php echo date('d/m/Y', strtotime($annee['date_fin'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Durée :</strong></td>
                        <td>
                            <?php 
                            $debut = new DateTime($annee['date_debut']);
                            $fin = new DateTime($annee['date_fin']);
                            $diff = $debut->diff($fin);
                            echo $diff->m . ' mois et ' . $diff->d . ' jours';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Statut :</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $annee['actif'] ? 'success' : 'secondary'; ?>">
                                <?php echo $annee['actif'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Créée le :</strong></td>
                        <td><?php echo date('d/m/Y à H:i', strtotime($annee['created_at'] ?? 'now')); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Résumé financier</h5>
            </div>
            <div class="card-body">
                <?php 
                $tauxPaiement = 0;
                // Calculer le taux de paiement si possible
                ?>
                <div class="mb-3">
                    <label class="form-label">Taux de recouvrement</label>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: <?php echo min($tauxPaiement, 100); ?>%;">
                            <?php echo round($tauxPaiement, 1); ?>%
                        </div>
                    </div>
                </div>
                
                <table class="table table-sm">
                    <tr>
                        <td>Total paiements reçus :</td>
                        <td class="text-end fw-bold"><?php echo number_format($annee['total_paiements'] ?? 0, 2); ?> FC</td>
                    </tr>
                    <tr>
                        <td>Nombre d'inscriptions :</td>
                        <td class="text-end"><?php echo $annee['nombre_inscriptions'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td>Nombre d'élèves :</td>
                        <td class="text-end"><?php echo $annee['nombre_eleves'] ?? 0; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (!($annee['actif'] ?? 0)): ?>
                    <a href="<?php echo SITE_URL; ?>/gestion/annee_activer.php?id=<?php echo $annee['id']; ?>" 
                       class="btn btn-success"
                       onclick="return confirm('Activer cette année ? Les autres années seront désactivées.')">
                        <i class="fas fa-check-circle"></i> Activer cette année
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo SITE_URL; ?>/gestion/calendriers.php" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt"></i> Gérer les calendriers de cette année
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/gestion/semestres.php?annee_id=<?php echo $annee['id']; ?>" class="btn btn-outline-info">
                        <i class="fas fa-list"></i> Voir les semestres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>