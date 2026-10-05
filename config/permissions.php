<?php
/**
 * Configuration des permissions par rôle
 */

// Définition des permissions
define('PERMISSION_ADMIN', 'admin');
define('PERMISSION_CAISSIER', 'caissier');
define('PERMISSION_SECRETAIRE', 'secretaire');
define('PERMISSION_COMPTABLE', 'comptable');

// Rôles et leurs permissions
$ROLE_PERMISSIONS = [
    'admin' => [
        'dashboard' => true,
        'eleves' => ['view', 'add', 'edit', 'delete'],
        'inscriptions' => ['view', 'add', 'edit', 'delete'],
        'paiements' => ['view', 'add', 'edit', 'delete'],
        'classes' => ['view', 'add', 'edit', 'delete'],
        'options' => ['view', 'add', 'edit', 'delete'],
        'frais' => ['view', 'add', 'edit', 'delete'],
        'types_frais' => ['view', 'add', 'edit', 'delete'],
        'calendriers' => ['view', 'add', 'edit', 'delete'],
        'annees' => ['view', 'add', 'edit', 'delete'],
        'utilisateurs' => ['view', 'add', 'edit', 'delete'],
        'rapports' => ['view', 'export'],
        'parametres' => ['view', 'edit'],
    ],
    'caissier' => [
        'dashboard' => true,
        'eleves' => ['view'],
        'inscriptions' => ['view'],
        'paiements' => ['view', 'add', 'edit'],
        'classes' => ['view'],
        'options' => ['view'],
        'frais' => ['view'],
        'types_frais' => ['view'],
        'calendriers' => ['view'],
        'annees' => ['view'],
        'utilisateurs' => [],
        'rapports' => ['view'],
        'parametres' => [],
    ],
    'secretaire' => [
        'dashboard' => true,
        'eleves' => ['view', 'add', 'edit'],
        'inscriptions' => ['view', 'add', 'edit'],
        
        'classes' => ['view'],
        'options' => ['view'],
        
        
        'calendriers' => ['view'],
        'annees' => ['view'],
        'utilisateurs' => [],
        'rapports' => ['view'],
        'parametres' => [],
    ],
    'comptable' => [
        'dashboard' => true,
        'eleves' => ['view'],
        'inscriptions' => ['view'],
        'paiements' => ['view', 'add', 'edit', 'delete'],
        'classes' => ['view'],
        'options' => ['view'],
        'frais' => ['view', 'add', 'edit'],
        'types_frais' => ['view', 'add', 'edit'],
        'calendriers' => ['view'],
        'annees' => ['view'],
        'utilisateurs' => [],
        'rapports' => ['view', 'export'],
        'parametres' => [],
    ],
];

/**
 * Vérifie si un utilisateur a une permission
 */
function hasPermission($userRole, $module, $action = 'view') {
    global $ROLE_PERMISSIONS;
    
    if (!isset($ROLE_PERMISSIONS[$userRole])) {
        return false;
    }
    
    if ($userRole === 'admin') {
        return true;
    }
    
    if (!isset($ROLE_PERMISSIONS[$userRole][$module])) {
        return false;
    }
    
    $permissions = $ROLE_PERMISSIONS[$userRole][$module];
    
    if ($permissions === true) {
        return true;
    }
    
    if (is_array($permissions)) {
        return in_array($action, $permissions);
    }
    
    return false;
}

/**
 * Vérifie si l'utilisateur connecté a une permission
 */
function userCan($module, $action = 'view') {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    return hasPermission($_SESSION['user_role'], $module, $action);
}

/**
 * Vérifie si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Vérifie si l'utilisateur est caissier
 */
function isCaissier() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'caissier';
}

/**
 * Vérifie si l'utilisateur est secrétaire
 */
function isSecretaire() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'secretaire';
}

/**
 * Vérifie si l'utilisateur est comptable
 */
function isComptable() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'comptable';
}

/**
 * Redirige si l'utilisateur n'a pas la permission
 */
function requirePermission($module, $action = 'view') {
    if (!userCan($module, $action)) {
        header('Location: ' . SITE_URL . '/access_denied.php');
        exit;
    }
}