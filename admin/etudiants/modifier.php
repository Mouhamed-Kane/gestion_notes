<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';
$etudiant = null;

// Récupération de l'étudiant
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT e.*, f.libelle as formation_nom 
                           FROM etudiants e 
                           LEFT JOIN formations f ON e.formation_id = f.id 
                           WHERE e.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $etudiant = $stmt->get_result()->fetch_assoc();

    if (!$etudiant) {
        header('Location: liste.php');
        exit();
    }
}

// Récupération des formations
$formations = $conn->query("SELECT * FROM formations ORDER BY libelle");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $adresse = mysqli_real_escape_string($conn, $_POST['adresse']);
    $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);
    $formation_id = mysqli_real_escape_string($conn, $_POST['formation_id']);
    
    // Vérification si le matricule existe déjà pour un autre étudiant
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $check = $conn->prepare("SELECT id FROM etudiants WHERE matricule = ? AND id != ?");
    $check->bind_param("si", $matricule, $id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Ce matricule est déjà utilisé par un autre étudiant";
    } else {
        // Mise à jour des informations
        $stmt = $conn->prepare("UPDATE etudiants SET 
                              matricule = ?, 
                              nom = ?, 
                              prenom = ?, 
                              adresse = ?, 
                              telephone = ?, 
                              formation_id = ? 
                              WHERE id = ?");
        
        $stmt->bind_param("sssssii", 
            $matricule, 
            $nom, 
            $prenom, 
            $adresse, 
            $telephone, 
            $formation_id, 
            $id
        );
        
        if ($stmt->execute()) {
            $success = "Les informations ont été mises à jour avec succès";
            
            // Réinitialisation du mot de passe si demandé
            if (isset($_POST['reset_password']) && $_POST['reset_password'] === '1') {
                $password_default = password_hash($matricule, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE etudiants SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $password_default, $id);
                if ($stmt->execute()) {
                    $success .= "<br>Le mot de passe a été réinitialisé au matricule : " . $matricule;
                }
            }
            
            // Mise à jour des données affichées
            $stmt = $conn->prepare("SELECT e.*, f.libelle as formation_nom 
                                  FROM etudiants e 
                                  LEFT JOIN formations f ON e.formation_id = f.id 
                                  WHERE e.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $etudiant = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Erreur lors de la mise à jour des informations";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Étudiant - Gestion des Notes</title>
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
                                <i class="fas fa-user-edit text-primary"></i>
                                Modifier l'Étudiant
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
                                <input type="hidden" name="id" value="<?php echo $etudiant['id']; ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="matricule" class="form-label">
                                                <i class="fas fa-id-card me-2"></i>Matricule
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="matricule" 
                                                   name="matricule" 
                                                   value="<?php echo htmlspecialchars($etudiant['matricule']); ?>" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="formation_id" class="form-label">
                                                <i class="fas fa-graduation-cap me-2"></i>Formation
                                            </label>
                                            <select class="form-select" id="formation_id" name="formation_id" required>
                                                <?php while ($formation = $formations->fetch_assoc()): ?>
                                                    <option value="<?php echo $formation['id']; ?>" 
                                                            <?php echo $formation['id'] == $etudiant['formation_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($formation['libelle']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nom" class="form-label">
                                                <i class="fas fa-user me-2"></i>Nom
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nom" 
                                                   name="nom" 
                                                   value="<?php echo htmlspecialchars($etudiant['nom']); ?>" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="prenom" class="form-label">
                                                <i class="fas fa-user me-2"></i>Prénom
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="prenom" 
                                                   name="prenom" 
                                                   value="<?php echo htmlspecialchars($etudiant['prenom']); ?>" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="telephone" class="form-label">
                                                <i class="fas fa-phone me-2"></i>Téléphone
                                            </label>
                                            <input type="tel" 
                                                   class="form-control" 
                                                   id="telephone" 
                                                   name="telephone" 
                                                   value="<?php echo htmlspecialchars($etudiant['telephone']); ?>" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="adresse" class="form-label">
                                                <i class="fas fa-map-marker-alt me-2"></i>Adresse
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="adresse" 
                                                   name="adresse" 
                                                   value="<?php echo htmlspecialchars($etudiant['adresse']); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mb-4">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="reset_password" 
                                           name="reset_password" 
                                           value="1">
                                    <label class="form-check-label" for="reset_password">
                                        <i class="fas fa-key me-2"></i>
                                        Réinitialiser le mot de passe au matricule
                                    </label>
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
