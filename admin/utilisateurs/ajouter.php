<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_conf = $_POST['mot_de_passe_conf'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (empty($mot_de_passe) || strlen($mot_de_passe) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($mot_de_passe !== $mot_de_passe_conf) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO utilisateurs (email, mot_de_passe, role) VALUES (?, ?, "administrateur")');
            $stmt->bind_param('ss', $email, $mot_de_passe_hash);
            if ($stmt->execute()) {
                $success = "Nouvel administrateur ajouté avec succès !";
            } else {
                $error = "Erreur lors de l'ajout de l'administrateur.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un administrateur - Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="main-content">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card fade-in">
                        <div class="card-body">
                            <h2 class="card-title mb-4 text-center">
                                <i class="fas fa-user-shield text-primary me-2"></i>
                                Ajouter un administrateur
                            </h2>
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
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">L'email est requis</div>
                                </div>
                                <div class="mb-3">
                                    <label for="mot_de_passe" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Mot de passe
                                    </label>
                                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="6">
                                    <div class="invalid-feedback">Mot de passe requis (min. 6 caractères)</div>
                                </div>
                                <div class="mb-3">
                                    <label for="mot_de_passe_conf" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Confirmer le mot de passe
                                    </label>
                                    <input type="password" class="form-control" id="mot_de_passe_conf" name="mot_de_passe_conf" required minlength="6">
                                    <div class="invalid-feedback">Veuillez confirmer le mot de passe</div>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Ajouter
                                    </button>
                                    <a href="../dashboard.php" class="btn btn-light btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
