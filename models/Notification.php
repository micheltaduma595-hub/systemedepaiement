<?php
require_once __DIR__ . '/Model.php';

class Notification extends Model {
    protected $table = 'notifications';
    
    public function creerNotificationPaiement($email, $montantPaye, $solde, $nomEleve) {
        $message = '<html><body>';
        $message .= '<h2>Confirmation de Paiement</h2>';
        $message .= '<p>Bonjour,</p>';
        $message .= '<p>Nous confirmons la réception du paiement pour l\'élève <strong>' . $nomEleve . '</strong>.</p>';
        $message .= '<table border="1" cellpadding="5" cellspacing="0">';
        $message .= '<tr><td><strong>Montant payé :</strong></td><td>' . number_format($montantPaye, 2) . ' FC</td></tr>';
        $message .= '<tr><td><strong>Solde restant :</strong></td><td>' . number_format($solde, 2) . ' FC</td></tr>';
        $message .= '</table>';
        $message .= '<p>Merci de votre confiance.</p>';
        $message .= '<p>Cordialement,<br>' . SCHOOL_NAME . '</p>';
        $message .= '</body></html>';
        
        return $this->create([
            'type' => 'paiement',
            'destinataire_email' => $email,
            'sujet' => 'Confirmation de paiement - ' . SCHOOL_NAME,
            'message' => $message
        ]);
    }
    
    public function creerRappelEcheance($email, $montantDu, $dateLimite, $nomEleve) {
        $message = '<html><body>';
        $message .= '<h2>Rappel de Paiement</h2>';
        $message .= '<p>Bonjour,</p>';
        $message .= '<p>Nous vous rappelons que le paiement pour l\'élève <strong>' . $nomEleve . '</strong> arrive à échéance.</p>';
        $message .= '<table border="1" cellpadding="5" cellspacing="0">';
        $message .= '<tr><td><strong>Montant dû :</strong></td><td>' . number_format($montantDu, 2) . ' FC</td></tr>';
        $message .= '<tr><td><strong>Date limite :</strong></td><td>' . date('d/m/Y', strtotime($dateLimite)) . '</td></tr>';
        $message .= '</table>';
        $message .= '<p>Veuillez régulariser votre situation dans les plus brefs délais.</p>';
        $message .= '<p>Cordialement,<br>' . SCHOOL_NAME . '</p>';
        $message .= '</body></html>';
        
        return $this->create([
            'type' => 'rappel',
            'destinataire_email' => $email,
            'sujet' => 'Rappel de paiement - ' . SCHOOL_NAME,
            'message' => $message
        ]);
    }
}