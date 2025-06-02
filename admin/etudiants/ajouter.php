<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';

// Récupération des formations
$formations = $conn->query("SELECT * FROM formations ORDER BY libelle");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $adresse = mysqli_real_escape_string($conn, $_POST['adresse']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $formation_id = mysqli_real_escape_string($conn, $_POST['formation_id']);
    $mot_de_passe = isset($_POST['mot_de_passe']) && $_POST['mot_de_passe'] !== '' ? $_POST['mot_de_passe'] : $matricule;
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Vérification si le matricule existe déjà
    $check = $conn->prepare("SELECT id FROM etudiants WHERE matricule = ?");
    $check->bind_param("s", $matricule);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Ce matricule existe déjà";
    } else {
        $stmt = $conn->prepare("INSERT INTO etudiants (matricule, nom, prenom, adresse, telephone, formation_id, mot_de_passe) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $matricule, $nom, $prenom, $adresse, $telephone, $formation_id, $mot_de_passe_hash);
        
        if ($stmt->execute()) {
            $success = "Étudiant ajouté avec succès. Mot de passe initial : " . $matricule;
        } else {
            $error = "Erreur lors de l'ajout de l'étudiant";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Étudiant - Gestion des Notes</title>
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
                        <a class="nav-link" href="liste.php">
                            <i class="fas fa-list"></i> Liste des étudiants
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
                                <i class="fas fa-user-plus text-primary"></i>
                                Ajouter un Étudiant
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

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="matricule" class="form-label">
                                                <i class="fas fa-id-card me-2"></i>Matricule
                                            </label>
                                            <input type="text" class="form-control" id="matricule" name="matricule" required>
                                            <div class="invalid-feedback">Le matricule est requis</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="formation_id" class="form-label">
                                                <i class="fas fa-graduation-cap me-2"></i>Formation
                                            </label>
                                            <select class="form-select" id="formation_id" name="formation_id" required>
                                                <option value="">Choisir une formation</option>
                                                <?php while ($formation = $formations->fetch_assoc()): ?>
                                                    <option value="<?php echo $formation['id']; ?>">
                                                        <?php echo htmlspecialchars($formation['libelle']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <div class="invalid-feedback">La formation est requise</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nom" class="form-label">
                                                <i class="fas fa-user me-2"></i>Nom
                                            </label>
                                            <input type="text" class="form-control" id="nom" name="nom" required>
                                            <div class="invalid-feedback">Le nom est requis</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="prenom" class="form-label">
                                                <i class="fas fa-user me-2"></i>Prénom
                                            </label>
                                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                                            <div class="invalid-feedback">Le prénom est requis</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="mot_de_passe" class="form-label">
                                                <i class="fas fa-lock me-2"></i>Mot de passe
                                            </label>
                                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe (laissé vide = matricule)" autocomplete="new-password">
                                            <div class="invalid-feedback">Le mot de passe est requis</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="telephone" class="form-label">
                                                <i class="fas fa-phone me-2"></i>Téléphone
                                            </label>
                                            <input type="tel" class="form-control" id="telephone" name="telephone" required>
                                            <div class="invalid-feedback">Le téléphone est requis</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="adresse" class="form-label">
                                                <i class="fas fa-map-marker-alt me-2"></i>Adresse
                                            </label>
                                            <input type="text" class="form-control" id="adresse" name="adresse" required>
                                            <div class="invalid-feedback">L'adresse est requise</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Enregistrer l'étudiant
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

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© 2025 Gestion des Notes. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activation de la validation Bootstrap
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
