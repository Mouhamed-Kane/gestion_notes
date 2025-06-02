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

    <!-- Section À propos -->
    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="https://img.freepik.com/free-vector/online-exams-concept-illustration_114360-7964.jpg?w=826&t=st=1718030000~exp=1718030600~hmac=demo" alt="À propos" class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-3">À propos de la plateforme</h2>
                    <p>
                        La plateforme <strong>Gestion des Notes</strong> a été conçue pour simplifier la gestion académique des étudiants et des administrateurs. Elle permet un accès rapide et sécurisé aux résultats, une gestion efficace des profils et un suivi détaillé des performances scolaires.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Interface intuitive et moderne</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Sécurité des données garantie</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Support technique réactif</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center mb-5">Témoignages</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <p class="mb-3">"La plateforme m'a permis de suivre mes notes facilement et d'améliorer mes résultats."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Etudiant" class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <strong>Yassine B.</strong><br>
                                Étudiant
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <p class="mb-3">"Gestion des Notes facilite la communication avec les étudiants et la gestion des résultats."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin" class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <strong>Fatima Z.</strong><br>
                                Administratrice
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <p class="mb-3">"Interface moderne, simple d'utilisation et support très réactif !"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/men/65.jpg" alt="Parent" class="rounded-circle me-3" width="48" height="48">
                            <div>
                                <strong>Omar K.</strong><br>
                                Parent d'élève
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Contact -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contactez-nous</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <input type="text" class="form-control" placeholder="Votre nom" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Votre email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Votre message" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Section FAQ -->
    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <h2 class="text-center mb-4">Foire Aux Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq1-heading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                            Comment puis-je accéder à mes notes ?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="faq1-heading" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Connectez-vous à votre espace étudiant pour consulter vos résultats et votre progression.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq2-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                            Mes données sont-elles sécurisées ?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-heading" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, la sécurité et la confidentialité de vos données sont notre priorité.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq3-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                            Qui contacter en cas de problème ?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-heading" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Utilisez le formulaire de contact ci-dessus ou adressez-vous à l'administration de votre établissement.
                        </div>
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
