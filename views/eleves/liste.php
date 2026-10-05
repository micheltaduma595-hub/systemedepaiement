<?php
$pageTitle = "Gestion des Élèves";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-graduate"></i> Liste des Élèves</h2>
    <div>
        <a href="eleves/ajouter.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel Élève
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Barre de recherche -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un élève...">
            </div>
            <div class="col-md-3">
                <select id="filterClasse" class="form-select">
                    <option value="">Toutes les classes</option>
                    <?php
                    $classes = array_unique(array_map(function($e) { 
                        return $e['classe_nom'] ?? 'Non inscrit'; 
                    }, $eleves));
                    foreach ($classes as $classe): 
                    ?>
                        <option value="<?php echo $classe; ?>"><?php echo $classe; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des élèves -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tableEleves">
                <thead class="table-dark">
                    <tr>
                        <th>Matricule</th>
                        <th>Photo</th>
                        <th>Nom complet</th>
                        <th>Genre</th>
                        <th>Classe</th>
                        <th>Téléphone</th>
                        <th>Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eleves as $eleve): ?>
                    <tr data-classe="<?php echo $eleve['classe_nom'] ?? 'Non inscrit'; ?>">
                        <td><span class="badge bg-secondary"><?php echo $eleve['matricule']; ?></span></td>
                        <td>
                            <img src="<?php echo $eleve['photo'] ?? 'assets/images/default-avatar.png'; ?>" 
                                 class="rounded-circle" width="40" height="40" 
                                 alt="<?php echo $eleve['nom']; ?>">
                        </td>
                        <td>
                            <strong><?php echo $eleve['nom']; ?></strong>
                            <?php echo $eleve['postnom'] ? $eleve['postnom'] . ' ' : ''; ?>
                            <?php echo $eleve['prenom']; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $eleve['genre'] == 'M' ? 'info' : 'danger'; ?>">
                                <?php echo $eleve['genre'] == 'M' ? 'M' : 'F'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($eleve['classe_nom']): ?>
                                <span class="badge bg-primary">
                                    <?php echo $eleve['classe_nom']; ?> (<?php echo $eleve['niveau']; ?>)
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning">Non inscrit</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $eleve['telephone'] ?? '-'; ?></td>
                        <td>
                            <small>
                                <?php echo $eleve['nom_parent'] ?? '-'; ?><br>
                                <?php echo $eleve['telephone_parent'] ?? ''; ?>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="eleves/fiche.php?id=<?php echo $eleve['id']; ?>" 
                                   class="btn btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="eleves/modifier.php?id=<?php echo $eleve['id']; ?>" 
                                   class="btn btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="eleves/supprimer.php?id=<?php echo $eleve['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')"
                                   title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // DataTable
    var table = $('#tableEleves').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
    
    // Recherche rapide
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // Filtre par classe
    $('#filterClasse').on('change', function() {
        var classe = $(this).val();
        if (classe) {
            table.column(4).search(classe).draw();
        } else {
            table.column(4).search('').draw();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../template.php';
?>