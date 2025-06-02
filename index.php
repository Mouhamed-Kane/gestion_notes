<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hero-section {
            padding: 100px 0;
            text-align: center;
        }
        .features-section {
            background: white;
            padding: 80px 0;
        }
        .feature-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            height: 100%;
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .login-buttons {
            margin-top: 40px;
        }
        .btn-custom {
            padding: 15px 40px;
            margin: 10px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        .footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Bienvenue sur la Plateforme de Gestion des Notes</h1>
            <p class="lead mb-5">Un outil simple et efficace pour gérer et consulter les notes des étudiants</p>
            <div class="login-buttons">
                <a href="login.php?type=etudiant" class="btn btn-primary btn-custom btn-lg">
                    <i class="fas fa-user-graduate me-2"></i> Espace Étudiant
                </a>
                <a href="login.php?type=admin" class="btn btn-secondary btn-custom btn-lg">
                    <i class="fas fa-user-shield me-2"></i> Espace Administrateur
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-search fa-2x mb-3 text-primary"></i>
                        <h3>Consultation des Notes</h3>
                        <p>Accédez facilement à vos résultats d'examens et suivez votre progression académique.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-user-cog fa-2x mb-3 text-primary"></i>
                        <h3>Gestion du Profil</h3>
                        <p>Gérez vos informations personnelles et mettez à jour votre mot de passe en toute sécurité.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-chart-line fa-2x mb-3 text-primary"></i>
                        <h3>Suivi des Performances</h3>
                        <p>Visualisez vos performances par matière et votre progression globale.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© 2025 Gestion des Notes. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
