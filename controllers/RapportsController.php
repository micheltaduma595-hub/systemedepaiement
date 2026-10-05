<?php
require_once 'Controller.php';
require_once __DIR__.'/../models/Solvabilite.php';

class RapportsController extends Controller {
    private $model;
    public function __construct() { $this->model = new Solvabilite(); }

    public function solvabilite() {
        $this->requireAuth();
        $filters = ['classe_id' => $_GET['classe_id'] ?? null, 'statut' => $_GET['statut'] ?? 'tous'];
        $data = $this->model->getEtatSolvabilite($filters);
        $classes = $this->model->getClasses();
        $this->render('rapports/solvabilite', ['data' => $data, 'classes' => $classes, 'filters' => $filters]);
    }

    public function exportPDF($type) {
        $this->requireAuth();
        $filters = ['classe_id' => $_GET['classe_id'] ?? null, 'statut' => $type];
        $data = $this->model->getEtatSolvabilite($filters);
        $titre = $type == 'solvable' ? 'Solvables' : 'Insolvables';
        $this->outputPDF($data, $titre);
    }

    private function outputPDF($data, $titre) {
        $html = '<!DOCTYPE html><html><head><title>'.$titre.'</title>';
        $html .= '<style>body{font-family:Arial;margin:20px}h2{text-align:center;color:'.PRIMARY_COLOR.'}';
        $html .= 'table{width:100%;border-collapse:collapse}th{background:'.PRIMARY_COLOR.';color:white;padding:8px}';
        $html .= 'td{padding:5px;border:1px solid #ddd}.total{font-weight:bold;background:#f0f0f0}';
        $html .= '@media print{button{display:none}}</style></head><body>';
        $html .= '<button onclick="window.print()" style="padding:10px 20px;margin-bottom:20px;cursor:pointer;background:'.PRIMARY_COLOR.';color:white;border:none;border-radius:5px;">Imprimer</button>';
        $html .= '<h2>'.SCHOOL_NAME.'</h2><h3>Liste des '.$titre.' - '.date('d/m/Y').'</h3>';
        $html .= '<table><thead><tr><th>Matricule</th><th>Nom</th><th>Classe</th><th>Total</th><th>Payé</th><th>Solde</th></tr></thead><tbody>';
        $tf = 0; $tp = 0;
        foreach ($data as $r) {
            $s = $r['total_frais'] - $r['total_paye'];
            $tf += $r['total_frais']; $tp += $r['total_paye'];
            $html .= '<tr><td>'.$r['matricule'].'</td><td>'.$r['nom'].' '.$r['prenom'].'</td><td>'.$r['classe'].'</td>';
            $html .= '<td align="right">'.number_format($r['total_frais'],2).'</td>';
            $html .= '<td align="right">'.number_format($r['total_paye'],2).'</td>';
            $html .= '<td align="right">'.number_format($s,2).'</td></tr>';
        }
        $html .= '<tr class="total"><td colspan="3">TOTAUX</td><td align="right">'.number_format($tf,2).'</td><td align="right">'.number_format($tp,2).'</td><td align="right">'.number_format($tf-$tp,2).'</td></tr>';
        $html .= '</tbody></table></body></html>';
        echo $html; exit;
    }
}
