<?php
session_start();
require_once '../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit();
}

// Récupération des statistiques
$stats = [
    'etudiants' => $conn->query("SELECT COUNT(*) as count FROM etudiants")->fetch_assoc()['count'],
    'formations' => $conn->query("SELECT COUNT(*) as count FROM formations")->fetch_assoc()['count'],
    'matieres' => $conn->query("SELECT COUNT(*) as count FROM matieres")->fetch_assoc()['count'],
    'notes' => $conn->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur - Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Tableau de Bord Administrateur
                    </h1>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card fade-in">
                        <i class="fas fa-user-graduate"></i>
                        <h3><?php echo $stats['etudiants']; ?></h3>
                        <p>Étudiants</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card fade-in">
                        <i class="fas fa-graduation-cap"></i>
                        <h3><?php echo $stats['formations']; ?></h3>
                        <p>Formations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card fade-in">
                        <i class="fas fa-book"></i>
                        <h3><?php echo $stats['matieres']; ?></h3>
                        <p>Matières</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card fade-in">
                        <i class="fas fa-star"></i>
                        <h3><?php echo $stats['notes']; ?></h3>
                        <p>Notes</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="section-title">Actions Rapides</h2>
                </div>
            </div>
            <div class="row g-4">
            <!-- Gestion des Utilisateurs -->
            <div class="col-md-4">
                <div class="card feature-card fade-in">
                    <div class="card-body">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield text-primary me-2"></i>
                            Gestion des Utilisateurs
                        </h3>
                        <div class="d-grid gap-2 mt-3">
                            <a href="utilisateurs/ajouter.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user-plus"></i> Ajouter un administrateur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                <!-- Gestion des Étudiants -->
                <div class="col-md-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body">
                            <h3 class="card-title">
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                Gestion des Étudiants
                            </h3>
                            <div class="d-grid gap-2 mt-3">
                                <a href="etudiants/ajouter.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus-circle"></i> Inscrire un étudiant
                                </a>
                                <a href="etudiants/liste.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list"></i> Liste des étudiants
                                </a>
                                <a href="etudiants/par_formation.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users"></i> Étudiants par formation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestion des Formations -->
                <div class="col-md-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap text-primary me-2"></i>
                                Gestion des Formations
                            </h3>
                            <div class="d-grid gap-2 mt-3">
                                <a href="formations/liste.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus-circle"></i> Ajouter une formation
                                </a>
                                <a href="formations/liste.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list"></i> Liste des formations
                                </a>
                                <a href="matieres/liste.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-book"></i> Ajouter une matière
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestion des Notes -->
                <div class="col-md-4">
                    <div class="card feature-card fade-in">
                        <div class="card-body">
                            <h3 class="card-title">
                                <i class="fas fa-star text-primary me-2"></i>
                                Gestion des Notes
                            </h3>
                            <div class="d-grid gap-2 mt-3">
                                <a href="notes/ajouter.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus-circle"></i> Ajouter des notes
                                </a>
                                <a href="notes/selection_etudiant.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i> Modifier des notes
                                </a>
                                <a href="notes/consulter.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-search"></i> Consulter les notes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© 2025 Gestion des Notes. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
