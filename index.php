<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Institut Technique Saint Komboni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0a1628 0%, #3e85f1 50%, #0a1628 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            padding-top: 80px;
        }

        /* Effet de fond avec motif géométrique */
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

        /* Hamburger menu */
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

        /* ============ HERO SECTION ============ */
        .hero-section {
            padding: 40px 0 60px 0;
            position: relative;
            z-index: 1;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.6);
            padding: 45px;
            transition: transform 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-card:hover {
            transform: translateY(-5px);
        }

        .school-logo {
            font-size: 4.5rem;
            color: #1a3a6b;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(26, 58, 107, 0.2);
        }

        .school-title {
            color: #0a1628;
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .school-title .highlight {
            color: #1a3a6b;
        }

        .school-subtitle {
            color: #4a5a7a;
            font-size: 1.1rem;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .school-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1a3a6b, #0a1628);
            color: white;
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .feature-box {
            background: #f0f4fa;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 15px;
            border-left: 4px solid #1a3a6b;
            transition: all 0.3s ease;
        }

        .feature-box:hover {
            background: #e4ebf5;
            transform: translateX(5px);
        }

        .feature-box i {
            color: #1a3a6b;
            font-size: 1.3rem;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }

        .feature-box strong {
            color: #0a1628;
        }

        .feature-box .text-muted {
            color: #5a6a8a !important;
            font-size: 0.85rem;
        }

        .stats-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e8ecf2;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a3a6b;
            display: block;
        }

        .stat-label {
            color: #5a6a8a;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .login-prompt {
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px 30px;
            color: white;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .login-prompt h3 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .login-prompt h3 span {
            color: #8ab4f8;
        }

        .login-prompt p {
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .login-prompt .btn-outline-light {
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .login-prompt .btn-outline-light:hover {
            background: #1a3a6b;
            border-color: #1a3a6b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(26, 58, 107, 0.3);
        }

        .login-prompt .btn-outline-light i {
            margin-right: 10px;
        }

        .school-motto {
            font-style: italic;
            font-size: 0.95rem;
            opacity: 0.7;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .quick-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .quick-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s ease;
        }

        .quick-links a:hover {
            color: #8ab4f8;
        }

        .quick-links span {
            color: rgba(255, 255, 255, 0.2);
        }

        /* Animation de vagues en bas */
        .wave-decoration {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='rgba(255,255,255,0.05)' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,213.3C672,224,768,224,864,213.3C960,203,1056,181,1152,181.3C1248,181,1344,203,1392,213.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'/%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
            pointer-events: none;
            z-index: 0;
        }

        /* ============ FOOTER ============ */
        .footer-custom {
            background: rgba(10, 22, 40, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            margin-top: 40px;
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

            .main-card {
                padding: 25px;
            }

            .school-title {
                font-size: 1.8rem;
            }

            .hero-section {
                padding: 20px 0 40px 0;
            }

            .login-prompt {
                padding: 30px 20px;
            }

            .login-prompt h3 {
                font-size: 1.4rem;
            }

            .stat-number {
                font-size: 1.4rem;
            }

            .wave-decoration {
                height: 30px;
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">
                            <i class="fas fa-cogs"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="fas fa-info-circle"></i> À propos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link nav-btn-connect" href="auth/login.php">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============ HERO SECTION ============ -->
    <div class="container hero-section">
        <div class="row align-items-center">
            <!-- Colonne de gauche - Informations -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="main-card">
                    <div class="text-center">
                        <i class="fas fa-school school-logo"></i>
                        <div class="school-badge">
                            <i class="fas fa-certificate me-1"></i> Enseignement Technique
                        </div>
                        <h1 class="school-title">
                            Institut Technique <span class="highlight">Saint Komboni</span>
                        </h1>
                        <p class="school-subtitle">
                            <i class="fas fa-map-marker-alt me-2" style="color: #1a3a6b;"></i>
                            Excellence & Formation Technique
                        </p>
                    </div>

                    <div class="mt-3">
                        <div class="feature-box">
                            <i class="fas fa-tools"></i>
                            <strong>Formations Techniques</strong>
                            <p class="mb-0 text-muted">Filières professionnelles adaptées au marché du travail</p>
                        </div>

                        <div class="feature-box">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <strong>Encadrement Qualifié</strong>
                            <p class="mb-0 text-muted">Corps professoral expérimenté et dévoué</p>
                        </div>

                        <div class="feature-box">
                            <i class="fas fa-award"></i>
                            <strong>Excellence Académique</strong>
                            <p class="mb-0 text-muted">Préparation aux examens et concours nationaux</p>
                        </div>
                    </div>

                    <div class="stats-section">
                        <div class="row">
                            <div class="col-4 stat-item">
                                <span class="stat-number">12+</span>
                                <span class="stat-label">Filières techniques</span>
                            </div>
                            <div class="col-4 stat-item">
                                <span class="stat-number">95%</span>
                                <span class="stat-label">Taux de réussite</span>
                            </div>
                            <div class="col-4 stat-item">
                                <span class="stat-number">25+</span>
                                <span class="stat-label">Années d'excellence</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite - Connexion -->
            <div class="col-lg-5">
                <div class="login-prompt">
                    <i class="fas fa-graduation-cap" style="font-size: 3rem; color: #8ab4f8; margin-bottom: 15px;"></i>
                    <h3>Espace <span>Élèves</span></h3>
                    <p>Accédez à vos cours, notes et ressources pédagogiques</p>
                    <a href="auth/login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                    <div class="school-motto">
                        "La formation technique, clé de l'avenir"
                    </div>
                </div>

                <!-- Liens rapides -->
                <div class="quick-links">
                    <a href="#"><i class="fas fa-info-circle me-1"></i> À propos</a>
                    <span>|</span>
                    <a href="#"><i class="fas fa-calendar-alt me-1"></i> Calendrier</a>
                    <span>|</span>
                    <a href="#"><i class="fas fa-phone me-1"></i> Contact</a>
                </div>

                <!-- Bandeau d'information -->
                <div class="mt-3 text-center text-white-50" style="font-size: 0.8rem;">
                    <i class="fas fa-shield-alt me-1"></i> Connexion sécurisée
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock me-1"></i> Accès 24/7
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

    <!-- ============ DÉCORATION VAGUE ============ -->
    <div class="wave-decoration"></div>

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
    </script>
</body>
</html>