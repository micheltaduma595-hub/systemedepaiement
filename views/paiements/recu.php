<?php
$pageTitle = "Reçu de Paiement N° " . $paiement['recu_numero'];
// Pas de ob_start() pour permettre l'impression directe
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            .recu-container { border: none !important; box-shadow: none !important; }
        }
        body { background: #f5f5f5; padding: 20px; }
        .recu-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .recu-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .recu-header h2 {
            color: <?php echo PRIMARY_COLOR; ?>;
            margin-bottom: 5px;
        }
        .recu-header h1 {
            font-size: 2em;
            margin: 10px 0;
        }
        .recu-number {
            font-size: 1.2em;
            font-weight: bold;
            color: #666;
        }
        .recu-body table {
            width: 100%;
        }
        .recu-body td {
            padding: 8px 5px;
            vertical-align: top;
        }
        .recu-body .label {
            font-weight: bold;
            width: 35%;
        }
        .montant {
            font-size: 1.3em;
            font-weight: bold;
            color: #28a745;
        }
        .recu-footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 6em;
            color: rgba(0,0,0,0.03);
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="no-print mb-3 text-center">
        <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
            <i class="fas fa-print"></i> Imprimer le reçu
        </button>
        <a href="../paiements.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <div class="recu-container" style="position: relative;">
        <div class="watermark">PAYÉ</div>
        
        <!-- En-tête du reçu -->
        <div class="recu-header">
            <?php if (defined('SCHOOL_LOGO') && SCHOOL_LOGO): ?>
                <img src="<?php echo SCHOOL_LOGO; ?>" alt="Logo" height="60" class="mb-2">
            <?php endif; ?>
            <h2><?php echo SCHOOL_NAME; ?></h2>
            <p><?php echo SCHOOL_ADDRESS; ?></p>
            <p>Tél: <?php echo SCHOOL_PHONE; ?> | Email: <?php echo SCHOOL_EMAIL; ?></p>
            <h1>REÇU DE PAIEMENT</h1>
            <div class="recu-number">N° <?php echo $paiement['recu_numero']; ?></div>
        </div>
        
        <!-- Corps du reçu -->
        <div class="recu-body">
            <table>
                <tr>
                    <td class="label">Date de paiement :</td>
                    <td><?php echo date('d/m/Y à H:i', strtotime($paiement['date_paiement'])); ?></td>
                </tr>
                <tr>
                    <td class="label">Matricule :</td>
                    <td><strong><?php echo $paiement['matricule']; ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Élève :</td>
                    <td><strong><?php echo $paiement['nom'] . ' ' . $paiement['postnom'] . ' ' . $paiement['prenom']; ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Classe :</td>
                    <td><?php echo $paiement['classe_nom'] . ' (' . $paiement['niveau'] . ')'; ?></td>
                </tr>
                <tr>
                    <td class="label">Option :</td>
                    <td><?php echo $paiement['option_nom']; ?></td>
                </tr>
                <tr><td colspan="2"><hr></td></tr>
                <tr>
                    <td class="label">Type de frais :</td>
                    <td><?php echo $paiement['type_frais']; ?></td>
                </tr>
                <tr>
                    <td class="label">Libellé :</td>
                    <td><?php echo $paiement['frais_libelle']; ?></td>
                </tr>
                <?php if ($paiement['echeance']): ?>
                <tr>
                    <td class="label">Échéance :</td>
                    <td><?php echo $paiement['echeance']; ?></td>
                </tr>
                <?php endif; ?>
                <tr><td colspan="2"><hr></td></tr>
                <tr>
                    <td class="label">Montant payé :</td>
                    <td class="montant"><?php echo number_format($paiement['montant_paye'], 2); ?> FC</td>
                </tr>
                <tr>
                    <td class="label">Mode de paiement :</td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $paiement['mode_paiement'])); ?></td>
                </tr>
                <?php if ($paiement['reference']): ?>
                <tr>
                    <td class="label">Référence :</td>
                    <td><?php echo $paiement['reference']; ?></td>
                </tr>
                <?php endif; ?>
            </table>
            
            <?php if ($paiement['commentaire']): ?>
            <div class="mt-3 p-2 bg-light">
                <strong>Commentaire :</strong> <?php echo $paiement['commentaire']; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pied du reçu -->
        <div class="recu-footer">
            <div class="signature">
                <div class="signature-line">Le Caissier</div>
                <small><?php echo $paiement['caissier_nom']; ?></small>
            </div>
            <div class="signature">
                <div class="signature-line">Le Parent/Élève</div>
                <small>Signature</small>
            </div>
            <div class="signature">
                <div class="signature-line">Cachet de l'école</div>
            </div>
        </div>
        
        <!-- Mention légale -->
        <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 0.8em; color: #999; text-align: center;">
            <p>Document généré le <?php echo date('d/m/Y à H:i'); ?> - Système de Gestion des Paiements</p>
            <p>Ce reçu tient lieu de quittance pour le montant indiqué ci-dessus.</p>
        </div>
    </div>
    
    <script>
        // Impression automatique
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>