<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email.php';

// Importer PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Charger l'autoloader de PHPMailer
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configuration du serveur
        $this->mailer->isSMTP();
        $this->mailer->Host       = MAIL_HOST;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = MAIL_USERNAME;
        $this->mailer->Password   = MAIL_PASSWORD;
        $this->mailer->SMTPSecure = MAIL_ENCRYPTION;
        $this->mailer->Port       = MAIL_PORT;
        $this->mailer->CharSet    = 'UTF-8';
        
        // Expéditeur
        $this->mailer->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $this->mailer->isHTML(true);
    }
    
    /**
     * Envoyer un email
     */
    public function envoyer($destinataire, $sujet, $message, $destinataireNom = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, $destinataireNom);
            $this->mailer->Subject = $sujet;
            $this->mailer->Body    = $message;
            $this->mailer->AltBody = strip_tags($message);
            
            $this->mailer->send();
            return ['success' => true, 'message' => 'Email envoyé avec succès'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Erreur: {$this->mailer->ErrorInfo}"];
        }
    }
    
    /**
     * Template d'email pour nouvelle inscription
     */
    public function envoyerEmailInscription($eleve, $classe, $frais) {
        $message = $this->getTemplateInscription($eleve, $classe, $frais);
        $sujet = 'Inscription confirmée - ' . SCHOOL_NAME;
        
        if (!empty($eleve['email_parent'])) {
            return $this->envoyer($eleve['email_parent'], $sujet, $message, $eleve['nom_parent'] ?? 'Parent');
        }
        
        return ['success' => false, 'message' => 'Aucun email parent trouvé'];
    }
    
    /**
     * Template d'email pour confirmation de paiement
     */
    public function envoyerEmailPaiement($paiement, $eleve, $solde) {
        $message = $this->getTemplatePaiement($paiement, $eleve, $solde);
        $sujet = 'Paiement reçu - ' . SCHOOL_NAME . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom'];
        
        $emails = [];
        if (!empty($eleve['email_parent'])) {
            $emails[] = $eleve['email_parent'];
        }
        if (!empty($eleve['email'])) {
            $emails[] = $eleve['email'];
        }
        
        if (empty($emails)) {
            return ['success' => false, 'message' => 'Aucun email trouvé'];
        }
        
        // Envoyer à tous les emails
        $resultats = [];
        foreach ($emails as $email) {
            $resultats[] = $this->envoyer($email, $sujet, $message);
        }
        
        return $resultats;
    }
    
    /**
     * Template d'email pour rappel d'échéance
     */
    public function envoyerRappelEcheance($eleve, $frais, $montantDu, $joursRestants) {
        $message = $this->getTemplateRappel($eleve, $frais, $montantDu, $joursRestants);
        $sujet = 'RAPPEL - Échéance de paiement - ' . SCHOOL_NAME;
        
        if (!empty($eleve['email_parent'])) {
            return $this->envoyer($eleve['email_parent'], $sujet, $message);
        }
        
        return ['success' => false, 'message' => 'Aucun email parent trouvé'];
    }
    
    /**
     * Template d'email pour relevé périodique
     */
    public function envoyerReleve($eleve, $classe, $frais, $paiements) {
        $message = $this->getTemplateReleve($eleve, $classe, $frais, $paiements);
        $sujet = 'Relevé de compte - ' . SCHOOL_NAME . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom'];
        
        if (!empty($eleve['email_parent'])) {
            return $this->envoyer($eleve['email_parent'], $sujet, $message);
        }
        
        return ['success' => false, 'message' => 'Aucun email parent trouvé'];
    }
    
    // ============ TEMPLATES HTML ============
    
    private function getTemplateInscription($eleve, $classe, $frais) {
        $html = $this->getHeaderEmail();
        $html .= '
        <div class="content">
            <h2>Bienvenue à ' . SCHOOL_NAME . ' !</h2>
            
            <p>Bonjour ' . ($eleve['nom_parent'] ?? 'Parent') . ',</p>
            
            <p>Nous avons le plaisir de vous confirmer l\'inscription de votre enfant 
               <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong> 
               pour l\'année académique en cours.</p>
            
            <div class="info-box">
                <h3>Informations de l\'élève</h3>
                <table>
                    <tr><td><strong>Matricule :</strong></td><td>' . $eleve['matricule'] . '</td></tr>
                    <tr><td><strong>Nom complet :</strong></td><td>' . $eleve['nom'] . ' ' . $eleve['postnom'] . ' ' . $eleve['prenom'] . '</td></tr>
                    <tr><td><strong>Classe :</strong></td><td>' . $classe['nom'] . ' (' . $classe['niveau'] . ')</td></tr>
                    <tr><td><strong>Option :</strong></td><td>' . $classe['option_nom'] . '</td></tr>
                </table>
            </div>';
        
        if (!empty($frais)) {
            $html .= '
            <div class="info-box">
                <h3>Frais à payer</h3>
                <table class="table-frais">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Libellé</th>
                            <th>Montant</th>
                            <th>Date limite</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $total = 0;
            foreach ($frais as $f) {
                $total += $f['montant'];
                $html .= '
                        <tr>
                            <td>' . $f['type_frais_libelle'] . '</td>
                            <td>' . $f['libelle'] . '</td>
                            <td>' . number_format($f['montant'], 2) . ' FC</td>
                            <td>' . ($f['date_limite'] ? date('d/m/Y', strtotime($f['date_limite'])) : 'Non définie') . '</td>
                        </tr>';
            }
            
            $html .= '
                        <tr class="total">
                            <td colspan="2"><strong>TOTAL</strong></td>
                            <td><strong>' . number_format($total, 2) . ' FC</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>';
        }
        
        $html .= '
            <div class="info-box">
                <h3>Modalités de paiement</h3>
                <ul>
                    <li>Les frais peuvent être payés en plusieurs tranches selon le calendrier établi</li>
                    <li>Modes de paiement acceptés : Espèce, Mobile Money, Virement bancaire</li>
                    <li>Les reçus de paiement vous seront envoyés par email</li>
                    <li>Pour toute question, contactez-nous : ' . SCHOOL_PHONE . '</li>
                </ul>
            </div>
            
            <p>Nous vous souhaitons une excellente année scolaire !</p>
            <p>Cordialement,<br>La Direction</p>
        </div>';
        
        $html .= $this->getFooterEmail();
        return $html;
    }
    
    private function getTemplatePaiement($paiement, $eleve, $solde) {
        $html = $this->getHeaderEmail();
        $html .= '
        <div class="content">
            <h2>Confirmation de Paiement</h2>
            
            <p>Bonjour,</p>
            
            <p>Nous accusons réception du paiement pour l\'élève 
               <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong>.</p>
            
            <div class="info-box" style="border-left: 4px solid #4CAF50;">
                <h3>Détails du paiement</h3>
                <table>
                    <tr><td><strong>N° Reçu :</strong></td><td>' . $paiement['recu_numero'] . '</td></tr>
                    <tr><td><strong>Date :</strong></td><td>' . date('d/m/Y à H:i', strtotime($paiement['date_paiement'])) . '</td></tr>
                    <tr><td><strong>Élève :</strong></td><td>' . $eleve['nom'] . ' ' . $eleve['prenom'] . ' (' . $eleve['matricule'] . ')</td></tr>
                    <tr><td><strong>Frais :</strong></td><td>' . $paiement['type_frais'] . ' - ' . $paiement['frais_libelle'] . '</td></tr>
                    <tr><td><strong>Montant payé :</strong></td><td style="font-size: 1.2em; color: #4CAF50;"><strong>' . number_format($paiement['montant_paye'], 2) . ' FC</strong></td></tr>
                    <tr><td><strong>Mode :</strong></td><td>' . ucfirst(str_replace('_', ' ', $paiement['mode_paiement'])) . '</td></tr>
                    <tr><td><strong>Solde restant :</strong></td><td>' . number_format($solde, 2) . ' FC</td></tr>
                </table>
            </div>
            
            <p>Ce reçu tient lieu de quittance. Conservez-le précieusement.</p>
            <p>Cordialement,<br>' . SCHOOL_NAME . '</p>
        </div>';
        
        $html .= $this->getFooterEmail();
        return $html;
    }
    
    private function getTemplateRappel($eleve, $frais, $montantDu, $joursRestants) {
        $html = $this->getHeaderEmail();
        $html .= '
        <div class="content">
            <h2 style="color: #ff6f00;">⚠ Rappel de Paiement</h2>
            
            <p>Bonjour ' . ($eleve['nom_parent'] ?? 'Parent') . ',</p>
            
            <p>Nous vous rappelons que le paiement suivant arrive à échéance pour votre enfant 
               <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong>.</p>
            
            <div class="info-box" style="border-left: 4px solid #ff6f00;">
                <table>
                    <tr><td><strong>Frais :</strong></td><td>' . $frais['libelle'] . '</td></tr>
                    <tr><td><strong>Montant dû :</strong></td><td style="color: red;"><strong>' . number_format($montantDu, 2) . ' FC</strong></td></tr>
                    <tr><td><strong>Date limite :</strong></td><td><strong>' . date('d/m/Y', strtotime($frais['date_limite'])) . '</strong></td></tr>
                    <tr><td><strong>Jours restants :</strong></td><td><strong>' . $joursRestants . ' jour(s)</strong></td></tr>
                </table>
            </div>
            
            <p>Nous vous prions de bien vouloir régulariser votre situation dans les plus brefs délais.</p>
            <p>Cordialement,<br>' . SCHOOL_NAME . '</p>
        </div>';
        
        $html .= $this->getFooterEmail();
        return $html;
    }
    
    private function getTemplateReleve($eleve, $classe, $frais, $paiements) {
        $html = $this->getHeaderEmail();
        $html .= '
        <div class="content">
            <h2>Relevé de Compte</h2>
            <p>Élève : <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong></p>
            <p>Classe : <strong>' . $classe['nom'] . '</strong></p>
            
            <h3>Frais</h3>
            <table class="table-frais">
                <thead><tr><th>Frais</th><th>Montant</th><th>Payé</th><th>Solde</th></tr></thead>
                <tbody>';
        
        $totalFrais = 0;
        $totalPaye = 0;
        
        foreach ($frais as $f) {
            $paye = 0;
            foreach ($paiements as $p) {
                if ($p['frais_id'] == $f['id']) {
                    $paye += $p['montant_paye'];
                }
            }
            $solde = $f['montant'] - $paye;
            $totalFrais += $f['montant'];
            $totalPaye += $paye;
            
            $html .= '<tr>
                <td>' . $f['libelle'] . '</td>
                <td>' . number_format($f['montant'], 2) . ' FC</td>
                <td>' . number_format($paye, 2) . ' FC</td>
                <td>' . number_format($solde, 2) . ' FC</td>
            </tr>';
        }
        
        $html .= '<tr class="total">
            <td><strong>TOTAL</strong></td>
            <td><strong>' . number_format($totalFrais, 2) . ' FC</strong></td>
            <td><strong>' . number_format($totalPaye, 2) . ' FC</strong></td>
            <td><strong>' . number_format($totalFrais - $totalPaye, 2) . ' FC</strong></td>
        </tr></tbody></table>
        
        <p>Cordialement,<br>' . SCHOOL_NAME . '</p>
        </div>';
        
        $html .= $this->getFooterEmail();
        return $html;
    }
    
    private function getHeaderEmail() {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
                .container { max-width: 600px; margin: 20px auto; background: white; }
                .header { background: ' . PRIMARY_COLOR . '; color: white; padding: 20px; text-align: center; }
                .header h1 { margin: 0; }
                .content { padding: 20px; }
                .info-box { background: #f9f9f9; border-left: 4px solid ' . PRIMARY_COLOR . '; padding: 15px; margin: 15px 0; }
                .info-box h3 { margin-top: 0; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                .table-frais th { background: #eee; padding: 10px; text-align: left; }
                .table-frais td { padding: 8px; border-bottom: 1px solid #ddd; }
                .total { font-weight: bold; background: #f0f0f0; }
                .footer { text-align: center; padding: 15px; background: #eee; font-size: 0.8em; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . SCHOOL_NAME . '</h1>
                    <p>' . SCHOOL_ADDRESS . '</p>
                </div>';
    }
    
    private function getFooterEmail() {
        return '
                <div class="footer">
                    <p>' . SCHOOL_NAME . ' | ' . SCHOOL_ADDRESS . '</p>
                    <p>Tél: ' . SCHOOL_PHONE . ' | Email: ' . SCHOOL_EMAIL . '</p>
                    <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
 * Email pour notification d'insolvabilité
 */
public function envoyerNotificationInsolvable($eleve, $trancheLibelle, $montantDu, $joursDepasses) {
    $message = $this->getTemplateInsolvable($eleve, $trancheLibelle, $montantDu, $joursDepasses);
    $sujet = 'URGENT - Paiement en retard - ' . SCHOOL_NAME . ' - ' . $eleve['nom'] . ' ' . $eleve['prenom'];
    
    if (!empty($eleve['email_parent'])) {
        return $this->envoyer($eleve['email_parent'], $sujet, $message, $eleve['nom_parent'] ?? 'Parent');
    }
    return ['success' => false, 'message' => 'Aucun email parent'];
}

/**
 * Email de félicitations pour solvabilité
 */
public function envoyerFelicitationsSolvabilite($eleve) {
    $message = $this->getTemplateFelicitations($eleve);
    $sujet = 'Félicitations - Tous les frais sont payés ! - ' . SCHOOL_NAME;
    
    if (!empty($eleve['email_parent'])) {
        return $this->envoyer($eleve['email_parent'], $sujet, $message, $eleve['nom_parent'] ?? 'Parent');
    }
    return ['success' => false, 'message' => 'Aucun email parent'];
}

/**
 * Template pour insolvable
 */
private function getTemplateInsolvable($eleve, $trancheLibelle, $montantDu, $joursDepasses) {
    $html = $this->getHeaderEmail();
    $html .= '
    <div class="content">
        <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; text-align: center; margin-bottom: 20px;">
            <h2 style="color: #856404; margin: 0;">⚠ AVIS DE RETARD DE PAIEMENT</h2>
        </div>
        
        <p>Bonjour ' . ($eleve['nom_parent'] ?? 'Parent') . ',</p>
        
        <p>Nous constatons que le paiement pour <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong> 
           est en retard de <strong style="color: red;">' . $joursDepasses . ' jour(s)</strong>.</p>
        
        <div class="info-box" style="border-left: 4px solid #dc3545; background: #fff5f5;">
            <h3 style="color: #dc3545;">Détails du retard</h3>
            <table>
                <tr><td><strong>Élève :</strong></td><td>' . $eleve['nom'] . ' ' . $eleve['prenom'] . ' (' . $eleve['matricule'] . ')</td></tr>
                <tr><td><strong>Classe :</strong></td><td>' . $eleve['classe_nom'] . '</td></tr>
                <tr><td><strong>Tranche concernée :</strong></td><td>' . $trancheLibelle . '</td></tr>
                <tr><td><strong>Montant dû :</strong></td><td style="font-size: 1.3em; color: #dc3545;"><strong>' . number_format($montantDu, 2) . ' FC</strong></td></tr>
                <tr><td><strong>Retard :</strong></td><td style="color: #dc3545;"><strong>' . $joursDepasses . ' jour(s)</strong></td></tr>
            </table>
        </div>
        
        <div style="background: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <p style="margin: 0; color: #721c24;">
                <strong>⚠ Important :</strong> Votre enfant est maintenant considéré comme <strong>insolvable</strong>. 
                Veuillez régulariser la situation dans les plus brefs délais pour éviter des perturbations.
            </p>
        </div>
        
        <p style="margin-top: 20px;">Nous restons à votre disposition pour toute information complémentaire.</p>
        <p>Cordialement,<br>' . SCHOOL_NAME . '</p>
    </div>';
    
    $html .= $this->getFooterEmail();
    return $html;
}

/**
 * Template pour félicitations
 */
private function getTemplateFelicitations($eleve) {
    $html = $this->getHeaderEmail();
    $html .= '
    <div class="content">
        <div style="background: #d4edda; border: 2px solid #28a745; padding: 15px; text-align: center; margin-bottom: 20px;">
            <h2 style="color: #155724; margin: 0;">🎉 FÉLICITATIONS !</h2>
        </div>
        
        <p>Bonjour ' . ($eleve['nom_parent'] ?? 'Parent') . ',</p>
        
        <p>Nous avons le plaisir de vous informer que tous les frais scolaires de 
           <strong>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</strong> sont maintenant <strong>entièrement payés</strong> !</p>
        
        <div class="info-box" style="border-left: 4px solid #28a745; background: #f0fff0;">
            <table>
                <tr><td><strong>Élève :</strong></td><td>' . $eleve['nom'] . ' ' . $eleve['prenom'] . '</td></tr>
                <tr><td><strong>Classe :</strong></td><td>' . $eleve['classe_nom'] . '</td></tr>
                <tr><td><strong>Statut :</strong></td><td style="color: #28a745;"><strong>SOLVABLE ✅</strong></td></tr>
            </table>
        </div>
        
        <p>Nous vous remercions pour votre diligence et votre confiance.</p>
        <p>Cordialement,<br>' . SCHOOL_NAME . '</p>
    </div>';
    
    $html .= $this->getFooterEmail();
    return $html;
}
}