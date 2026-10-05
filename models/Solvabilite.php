<?php
require_once 'Model.php';

class Solvabilite extends Model {
    protected $table = 'inscriptions';

    public function getEtatSolvabilite($filters = []) {
        $sql = "SELECT e.id as eleve_id, e.matricule, e.nom, e.prenom,
            c.nom as classe, o.nom as option_nom,
            COALESCE((SELECT SUM(montant) FROM frais WHERE classe_id=c.id AND statut='actif'),0) as total_frais,
            COALESCE((SELECT SUM(montant_paye) FROM paiements WHERE inscription_id=i.id AND statut='valide'),0) as total_paye
        FROM inscriptions i
        JOIN eleves e ON i.eleve_id=e.id
        JOIN classes c ON i.classe_id=c.id
        JOIN options o ON c.option_id=o.id
        WHERE i.statut='actif'";
        $params = [];
        if (!empty($filters['classe_id'])) { $sql .= ' AND c.id=:cid'; $params['cid'] = $filters['classe_id']; }
        $sql .= ' GROUP BY e.id, c.id, i.id';
        if (!empty($filters['statut']) && $filters['statut'] != 'tous') {
            $sql .= ' HAVING ';
            if ($filters['statut'] == 'solvable') $sql .= 'total_paye >= total_frais';
            else $sql .= 'total_paye < total_frais';
        }
        $sql .= ' ORDER BY c.nom, e.nom';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getClasses() {
        return $this->db->query('SELECT id, nom, niveau FROM classes WHERE actif=1 ORDER BY nom')->fetchAll();
    }

    public function getPaiementDetail($id) {
        $sql = "SELECT p.*, e.matricule, e.nom, e.prenom, c.nom as classe,
            f.libelle as frais_libelle, u.nom as cree_par_nom
        FROM paiements p
        JOIN inscriptions i ON p.inscription_id=i.id
        JOIN eleves e ON i.eleve_id=e.id
        JOIN classes c ON i.classe_id=c.id
        JOIN frais f ON p.frais_id=f.id
        LEFT JOIN utilisateurs u ON p.created_by=u.id
        WHERE p.id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
