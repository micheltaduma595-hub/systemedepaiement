<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/permissions.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rediriger si déjà connecté
if(isset($_SESSION['user_id'])){
    header('Location: ../dashboard.php');
    exit;
}

$error = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupérer l'utilisateur
        $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE email = ? AND actif = 1');
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($_POST['password'], $user['mot_de_passe'])){
            // Récupérer le rôle de l'utilisateur
            $stmt = $db->prepare("
                SELECT r.nom 
                FROM utilisateur_roles ur
                JOIN roles r ON ur.role_id = r.id
                WHERE ur.utilisateur_id = ?
            ");
            $stmt->execute([$user['id']]);
            $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Si aucun rôle n'est trouvé, assigner un rôle par défaut
            if (empty($roles)) {
                $roles = ['user'];
            }
            
            // Stocker les informations en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $roles[0]; // Rôle principal
            $_SESSION['user_roles'] = $roles; // Tous les rôles
            
            // Mettre à jour la dernière connexion
            $stmt = $db->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Rediriger vers le dashboard
            header('Location: ../dashboard.php');
            exit;
        }
        $error = 'Email ou mot de passe incorrect';
    } catch (Exception $e) {
        $error = 'Une erreur est survenue. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo SCHOOL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3a6b;
            --primary-dark: #0a1628;
            --primary-light: #3e85f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0a1628 0%, #3e85f1 50%, #0a1628 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
            position: relative;
        }

        /* Effet de fond */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ============ HEADER / NAVBAR ============ */
        .navbar-custom {
            background: rgba(10, 22, 40, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 12px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            border-bottom: 2px solid rgba(62, 133, 241, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            background: rgba(10, 22, 40, 0.98);
            padding: 8px 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white !important;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
        }

        .navbar-brand i {
            font-size: 2rem;
            color: #5b9af5;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover i {
            transform: rotate(-10deg) scale(1.1);
        }

        .navbar-brand .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .navbar-brand .brand-text .school-name {
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .navbar-brand .brand-text .school-sub {
            font-size: 0.65rem;
            color: #8ab4f8;
            font-weight: 400;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 10px 18px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(62, 133, 241, 0.2);
        }

        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(62, 133, 241, 0.3);
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: #5b9af5;
            border-radius: 10px;
        }

        .nav-btn-connect {
            background: linear-gradient(135deg, #3e85f1, #1a3a6b) !important;
            color: white !important;
            border-radius: 50px !important;
            padding: 8px 25px !important;
            font-weight: 600 !important;
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease !important;
        }

        .nav-btn-connect:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(62, 133, 241, 0.4);
            background: linear-gradient(135deg, #5b9af5, #2a4a7b) !important;
        }

        .nav-btn-connect i {
            margin-right: 8px;
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 12px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ============ CARD DE CONNEXION ============ */
        .login-wrapper {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 160px);
            display: flex;
            align-items: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            padding: 45px 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .login-icon {
            font-size: 3.5rem;
            color: #1a3a6b;
            margin-bottom: 10px;
        }

        .login-header h3 {
            color: #0a1628;
            font-weight: 800;
            font-size: 1.8rem;
        }

        .login-header h3 span {
            color: #1a3a6b;
        }

        .login-header p {
            color: #5a6a8a;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .login-header .divider {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #1a3a6b, #3e85f1);
            margin: 15px auto 0;
            border-radius: 10px;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e8ecf2;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3e85f1;
            box-shadow: 0 0 0 4px rgba(62, 133, 241, 0.15);
        }

        .form-control.input-icon {
            padding-left: 45px;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #8a9ab0;
            font-size: 1.1rem;
            z-index: 10;
        }

        .btn-login-submit {
            background: linear-gradient(135deg, #1a3a6b, #0a1628);
            border: none;
            padding: 14px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1rem;
            width: 100%;
        }

        .btn-login-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(26, 58, 107, 0.4);
            background: linear-gradient(135deg, #2a4a7b, #1a2a4a);
            color: white;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e8ecf2;
        }

        .login-footer a {
            color: #1a3a6b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #3e85f1;
        }

        .login-footer .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8a9ab0;
            font-size: 0.8rem;
            margin-top: 10px;
        }

        .login-footer .security-badge i {
            color: #3e85f1;
        }

        /* ============ FOOTER ============ */
        .footer-custom {
            background: rgba(10, 22, 40, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 1;
        }

        .footer-custom p {
            margin: 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        .footer-custom a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-custom a:hover {
            color: #8ab4f8;
        }

        .footer-custom .social-icons a {
            display: inline-block;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.5);
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .footer-custom .social-icons a:hover {
            background: rgba(62, 133, 241, 0.3);
            color: white;
            transform: translateY(-3px);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .navbar-brand .brand-text .school-name {
                font-size: 0.9rem;
            }

            .navbar-brand .brand-text .school-sub {
                font-size: 0.55rem;
            }

            .login-card {
                padding: 30px 20px;
            }

            .login-header h3 {
                font-size: 1.4rem;
            }

            .login-header .login-icon {
                font-size: 2.8rem;
            }

            .navbar-nav .nav-link {
                text-align: center;
                padding: 12px 18px !important;
            }

            .nav-btn-connect {
                text-align: center !important;
                margin-top: 5px;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 25px 15px;
            }

            .form-control {
                padding: 12px 15px;
                font-size: 0.95rem;
            }

            .form-control.input-icon {
                padding-left: 40px;
            }
        }

        /* Animation de chargement */
        .spinner-loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease-in-out infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login-submit.loading .btn-text {
            display: none;
        }

        .btn-login-submit.loading .spinner-loading {
            display: inline-block;
        }
    </style>
</head>
<body>

    <!-- ============ HEADER / NAVBAR ============ -->
    <nav class="navbar navbar-expand-lg navbar-custom" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-school"></i>
                <span class="brand-text">
                    <span class="school-name">Institut Technique Saint Komboni</span>
                    <span class="school-sub">Enseignement Technique</span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">
                            <i class="fas fa-cogs"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">
                            <i class="fas fa-info-circle"></i> À propos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link nav-btn-connect active" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============ LOGIN SECTION ============ -->
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-card">
                        <div class="login-header">
                            <i class="fas fa-user-graduate login-icon"></i>
                            <h3>Espace <span>Connexion</span></h3>
                            <p>Connectez-vous à votre compte</p>
                            <div class="divider"></div>
                        </div>

                        <?php if($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3 input-group-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" id="email" class="form-control input-icon" 
                                       placeholder="Adresse email" required 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="mb-3 input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" class="form-control input-icon" 
                                       placeholder="Mot de passe" required minlength="6">
                            </div>
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" style="color: #5a6a8a; font-size: 0.9rem;" for="remember">
                                        Se souvenir de moi
                                    </label>
                                </div>
                                <a href="#" style="color: #1a3a6b; font-size: 0.9rem; text-decoration: none;">
                                    Mot de passe oublié ?
                                </a>
                            </div>
                            <button type="submit" id="loginBtn" class="btn-login-submit">
                                <span class="btn-text"><i class="fas fa-sign-in-alt me-2"></i> Se connecter</span>
                                <span class="spinner-loading"></span>
                            </button>
                        </form>

                        <div class="login-footer">
                            <p style="color: #5a6a8a; margin-bottom: 0;">
                                Pas encore de compte ? 
                                <a href="#">S'inscrire</a>
                            </p>
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                Connexion sécurisée
                                <span class="mx-2">•</span>
                                <i class="fas fa-lock"></i>
                                SSL/HTTPS
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ FOOTER ============ -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p>
                        &copy; <?php echo date('Y'); ?> 
                        <a href="index.php">Institut Technique Saint Komboni</a> - 
                        Tous droits réservés
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="social-icons">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Effet de scroll sur la navbar
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Fermer le menu mobile après un clic
        document.querySelectorAll('.navbar-nav .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                const navbarCollapse = document.getElementById('navbarNav');
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            });
        });

        // Animation du bouton de connexion
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Gestion de la touche Entrée
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });

        // Focus sur le champ email au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.focus();
            }
        });
    </script>
</body>
</html>