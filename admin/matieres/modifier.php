<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';
$matiere = null;

// Récupération de la matière
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM matieres WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $matiere = $stmt->get_result()->fetch_assoc();

    if (!$matiere) {
        header('Location: liste.php');
        exit();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $code = trim($_POST['code']);
    $libelle = trim($_POST['libelle']);
    $coefficient = (float)$_POST['coefficient'];

    // Vérification si le code existe déjà pour une autre matière
    $check = $conn->prepare("SELECT id FROM matieres WHERE code = ? AND id != ?");
    $check->bind_param("si", $code, $id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $error = "Une autre matière utilise déjà ce code";
    } else {
        $stmt = $conn->prepare("UPDATE matieres SET code = ?, libelle = ?, coefficient = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $code, $libelle, $coefficient, $id);
        
        if ($stmt->execute()) {
            $success = "La matière a été mise à jour avec succès";
            
            // Mise à jour des données affichées
            $stmt = $conn->prepare("SELECT * FROM matieres WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $matiere = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Erreur lors de la mise à jour de la matière";
        }
    }
}

// Récupération des statistiques
$stmt = $conn->prepare("
    SELECT COUNT(*) as nb_notes,
           AVG(note) as moyenne_generale,
           MIN(note) as note_min,
           MAX(note) as note_max
    FROM notes 
    WHERE matiere_id = ?
");
$stmt->bind_param("i", $matiere['id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Matière - Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../etudiants/liste.php">
                            <i class="fas fa-user-graduate"></i> Étudiants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../formations/liste.php">
                            <i class="fas fa-graduation-cap"></i> Formations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste.php">
                            <i class="fas fa-book"></i> Matières
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card fade-in">
                        <div class="card-body">
                            <h1 class="card-title text-center mb-4">
                                <i class="fas fa-edit text-primary"></i>
                                Modifier une Matière
                            </h1>

                            <?php if ($success): ?>
                                <div class="alert alert-success fade-in">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                                <div class="alert alert-danger fade-in">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Statistiques -->
                            <?php if ($stats['nb_notes'] > 0): ?>
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">
                                                    <i class="fas fa-calculator text-primary"></i>
                                                    Moyenne Générale
                                                </h5>
                                                <h3 class="mb-0 <?php echo $stats['moyenne_generale'] >= 10 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo number_format($stats['moyenne_generale'], 2); ?>/20
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">
                                                    <i class="fas fa-chart-bar text-primary"></i>
                                                    Notes Min/Max
                                                </h5>
                                                <h3 class="mb-0">
                                                    <span class="text-danger"><?php echo $stats['note_min']; ?></span>
                                                    /
                                                    <span class="text-success"><?php echo $stats['note_max']; ?></span>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucune note n'a encore été enregistrée pour cette matière.
                                </div>
                            <?php endif; ?>

                            <!-- Formulaire de modification -->
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $matiere['id']; ?>">
                                
                                <div class="mb-4">
                                    <label for="code" class="form-label">
                                        <i class="fas fa-hashtag me-2"></i>Code
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="code" 
                                           name="code" 
                                           value="<?php echo htmlspecialchars($matiere['code']); ?>"
                                           required 
                                           pattern="[A-Za-z0-9-_]+"
                                           maxlength="10">
                                    <div class="invalid-feedback">
                                        Le code est requis et ne doit contenir que des lettres, chiffres, tirets et underscores
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="libelle" class="form-label">
                                        <i class="fas fa-font me-2"></i>Libellé
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="libelle" 
                                           name="libelle" 
                                           value="<?php echo htmlspecialchars($matiere['libelle']); ?>"
                                           required>
                                    <div class="invalid-feedback">
                                        Le libellé est requis
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="coefficient" class="form-label">
                                        <i class="fas fa-balance-scale me-2"></i>Coefficient
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="coefficient" 
                                           name="coefficient" 
                                           value="<?php echo $matiere['coefficient']; ?>"
                                           step="0.5"
                                           min="0.5"
                                           required>
                                    <div class="invalid-feedback">
                                        Le coefficient doit être supérieur à 0
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>
                                        Enregistrer les modifications
                                    </button>
                                    <a href="liste.php" class="btn btn-light btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Retour à la liste
                                    </a>
                                </div>
                            </form>
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
    <script>
        // Validation Bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
