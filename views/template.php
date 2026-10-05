<?php
$school = defined('SCHOOL_NAME') ? SCHOOL_NAME : 'Institut Technique Saint Komboni';
$color = defined('PRIMARY_COLOR') ? PRIMARY_COLOR : '#1a3a6b';

// Charger les permissions
require_once __DIR__ . '/../config/permissions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? $school; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: <?php echo $color; ?>;
            --primary-dark: #0a1628;
            --primary-light: #3e85f1;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f9;
            min-height: 100vh;
            padding-top: var(--header-height);
        }

        /* ============ HEADER ============ */
        .navbar-custom {
            background: linear-gradient(135deg, #0a1628 0%, var(--primary) 100%);
            height: var(--header-height);
            padding: 0 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            border-bottom: 2px solid rgba(62, 133, 241, 0.3);
        }

        .navbar-custom .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white !important;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .navbar-custom .navbar-brand i {
            font-size: 1.8rem;
            color: #5b9af5;
        }

        .navbar-custom .navbar-brand .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .navbar-custom .navbar-brand .brand-text .school-name {
            font-size: 1rem;
        }

        .navbar-custom .navbar-brand .brand-text .school-sub {
            font-size: 0.6rem;
            color: #8ab4f8;
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar-custom .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .navbar-custom .user-info .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .navbar-custom .user-info .user-name {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .navbar-custom .user-info .user-role {
            font-size: 0.7rem;
            opacity: 0.7;
            display: block;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 50px;
            padding: 6px 20px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-logout i {
            font-size: 0.9rem;
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 10px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ============ SIDEBAR ============ */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0a1628 0%, #1a2a4a 100%);
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1040;
            transition: transform 0.3s ease;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 15px;
        }

        .sidebar .sidebar-header small {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        .sidebar .nav {
            padding: 0 10px;
        }

        .sidebar .nav-item {
            margin-bottom: 2px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.6);
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.3);
            transition: color 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar .nav-link:hover i {
            color: #5b9af5;
        }

        .sidebar .nav-link.active {
            color: white;
            background: rgba(62, 133, 241, 0.2);
            border-left: 3px solid #5b9af5;
        }

        .sidebar .nav-link.active i {
            color: #5b9af5;
        }

        .sidebar .nav-link .badge {
            margin-left: auto;
            background: rgba(62, 133, 241, 0.3);
            color: #8ab4f8;
            font-weight: 600;
            font-size: 0.7rem;
            padding: 2px 10px;
            border-radius: 50px;
        }

        .sidebar .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.05);
            margin: 10px 15px;
        }

        /* ============ MAIN CONTENT ============ */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px 30px;
            min-height: calc(100vh - var(--header-height));
            transition: margin-left 0.3s ease;
        }

        /* ============ PAGE HEADER ============ */
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h4 {
            font-weight: 700;
            color: #0a1628;
            margin: 0;
        }

        .page-header h4 small {
            font-weight: 400;
            color: #6b7a8a;
            font-size: 0.85rem;
            margin-left: 10px;
        }

        .page-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .page-header .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }

        .page-header .breadcrumb-item.active {
            color: #6b7a8a;
        }

        /* ============ CARDS ============ */
        .card-custom {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        /* ============ STATS CARDS ============ */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0a1628;
            line-height: 1;
        }

        .stat-card .stat-label {
            color: #6b7a8a;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ============ BUTTONS ============ */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(26, 58, 107, 0.3);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* ============ RESPONSIVE ============ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: var(--header-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .navbar-custom .navbar-brand .brand-text .school-name {
                font-size: 0.85rem;
            }

            .navbar-custom .user-info .user-name {
                display: none;
            }
        }

        @media (max-width: 576px) {
            :root {
                --header-height: 60px;
            }

            .main-content {
                padding: 15px;
            }

            .page-header {
                padding: 15px;
                flex-direction: column;
                align-items: flex-start;
            }

            .stat-card .stat-number {
                font-size: 1.5rem;
            }

            .navbar-custom .navbar-brand i {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

    <!-- ============ HEADER ============ -->
    <nav class="navbar navbar-custom navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo defined('SITE_URL') ? SITE_URL : '/'; ?>">
                <i class="fas fa-school"></i>
                <span class="brand-text">
                    <span class="school-name"><?php echo $school; ?></span>
                    <span class="school-sub">Gestion Scolaire</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarUser">
                <?php if(isset($_SESSION['user_id'])): ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div>
                                <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'Utilisateur'; ?></span>
                                <span class="user-role">
                                    <?php 
                                    $roleLabels = [
                                        'admin' => 'Administrateur',
                                        'caissier' => 'Caissier',
                                        'secretaire' => 'Secrétaire',
                                        'comptable' => 'Comptable'
                                    ];
                                    echo $roleLabels[$_SESSION['user_role']] ?? $_SESSION['user_role'] ?? 'Utilisateur';
                                    ?>
                                </span>
                            </div>
                            <a href="<?php echo defined('SITE_URL') ? SITE_URL . '/auth/logout.php' : '/auth/logout.php'; ?>" class="btn-logout">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </div>
                    </li>
                </ul>
                <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="<?php echo defined('SITE_URL') ? SITE_URL . '/auth/login.php' : '/auth/login.php'; ?>" class="btn-logout">
                            <i class="fas fa-sign-in-alt"></i> Connexion
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ============ SIDEBAR OVERLAY ============ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ============ SIDEBAR ============ -->
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <small><i class="fas fa-th-large me-1"></i> Navigation principale</small>
        </div>
        <ul class="nav flex-column">
            <!-- Tableau de bord -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/dashboard.php' : '/dashboard.php'; ?>">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
            </li>

            <!-- Élèves -->
            <?php if(userCan('eleves', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'eleves.php' || strpos($_SERVER['PHP_SELF'], 'eleves/') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/eleves.php' : '/eleves.php'; ?>">
                    <i class="fas fa-users"></i> Élèves
                    <?php if(userCan('eleves', 'add')): ?>
                    <span class="badge">+</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <!-- Paiements -->
            <?php if(userCan('paiements', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'paiements.php' ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/paiements.php' : '/paiements.php'; ?>">
                    <i class="fas fa-money-bill-wave"></i> Paiements
                    <?php if(userCan('paiements', 'add')): ?>
                    <span class="badge">+</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <!-- Solvabilité -->
            <?php if(userCan('rapports', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'solvabilite') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/rapports/solvabilite.php' : '/rapports/solvabilite.php'; ?>">
                    <i class="fas fa-check-circle"></i> Solvabilité
                </a>
            </li>
            <?php endif; ?>

            <!-- Gestion - Section -->
            <?php if(userCan('options', 'view') || userCan('classes', 'view') || userCan('frais', 'view') || userCan('types_frais', 'view') || userCan('calendriers', 'view') || userCan('annees', 'view')): ?>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <small style="color:rgba(255,255,255,0.3); padding: 0 16px 8px; font-size:0.65rem; text-transform:uppercase; letter-spacing:1px; display:block;">
                    <i class="fas fa-cog me-1"></i> Gestion
                </small>
            </li>
            <?php endif; ?>

            <!-- Options -->
            <?php if(userCan('options', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'options.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/options.php' : '/gestion/options.php'; ?>">
                    <i class="fas fa-layer-group"></i> Options
                </a>
            </li>
            <?php endif; ?>

            <!-- Classes -->
            <?php if(userCan('classes', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'classes.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/classes.php' : '/gestion/classes.php'; ?>">
                    <i class="fas fa-school"></i> Classes
                </a>
            </li>
            <?php endif; ?>

            <!-- Frais -->
            <?php if(userCan('frais', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'frais.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/frais.php' : '/gestion/frais.php'; ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Frais
                </a>
            </li>
            <?php endif; ?>

            <!-- Types Frais -->
            <?php if(userCan('types_frais', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'types_frais.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/types_frais.php' : '/gestion/types_frais.php'; ?>">
                    <i class="fas fa-tags"></i> Types Frais
                </a>
            </li>
            <?php endif; ?>

            <!-- Calendrier -->
            <?php if(userCan('calendriers', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'calendriers.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/calendriers.php' : '/gestion/calendriers.php'; ?>">
                    <i class="fas fa-calendar-alt"></i> Calendrier
                </a>
            </li>
            <?php endif; ?>

            <!-- Années Scolaires -->
            <?php if(userCan('annees', 'view')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'annees.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/annees.php' : '/gestion/annees.php'; ?>">
                    <i class="fas fa-calendar"></i> Années Scolaires
                </a>
            </li>
            <?php endif; ?>

            <!-- Administration - Section (Admin uniquement) -->
            <?php if(isAdmin()): ?>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <small style="color:rgba(255,255,255,0.3); padding: 0 16px 8px; font-size:0.65rem; text-transform:uppercase; letter-spacing:1px; display:block;">
                    <i class="fas fa-shield-alt me-1"></i> Administration
                </small>
            </li>

            <!-- Utilisateurs -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'utilisateurs.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/utilisateurs.php' : '/gestion/utilisateurs.php'; ?>">
                    <i class="fas fa-users-cog"></i> Utilisateurs
                    <span class="badge">
                        <?php 
                        try {
                            $db = Database::getInstance()->getConnection();
                            $count = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE actif = 1")->fetchColumn();
                            echo $count;
                        } catch (Exception $e) {
                            echo '0';
                        }
                        ?>
                    </span>
                </a>
            </li>

            <!-- Paramètres -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'parametres.php') !== false ? 'active' : ''; ?>" 
                   href="<?php echo defined('SITE_URL') ? SITE_URL . '/gestion/parametres.php' : '/gestion/parametres.php'; ?>">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="main-content" id="mainContent">
        <!-- Page Header -->
        <?php if(isset($pageTitle)): ?>
        <div class="page-header">
            <div>
                <h4>
                    <?php echo $pageTitle; ?>
                    <?php if(isset($pageSubtitle)): ?>
                        <small><?php echo $pageSubtitle; ?></small>
                    <?php endif; ?>
                </h4>
                <?php if(isset($breadcrumb)): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo defined('SITE_URL') ? SITE_URL . '/' : '/'; ?>">Accueil</a></li>
                        <?php foreach($breadcrumb as $label => $url): ?>
                            <?php if(is_string($label)): ?>
                                <li class="breadcrumb-item"><a href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?php echo $url; ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
            <?php if(isset($pageActions)): ?>
            <div>
                <?php echo $pageActions; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Content -->
        <?php echo $content ?? ''; ?>
    </div>

    <!-- ============ SCRIPTS ============ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.navbar-toggler')?.addEventListener('click', function(e) {
            e.preventDefault();
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Close sidebar on link click (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        // Initialize DataTables if they exist
        document.querySelectorAll('.datatable').forEach(function(table) {
            $(table).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                },
                pageLength: 25,
                responsive: true
            });
        });

        // Tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    </script>
</body>
</html>