<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';
$note = null;

// Récupération de la note
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("
        SELECT n.*, 
               e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.matricule,
               m.code as matiere_code, m.libelle as matiere_nom,
               f.libelle as formation_nom
        FROM notes n
        JOIN etudiants e ON n.etudiant_id = e.id
        JOIN matieres m ON n.matiere_id = m.id
        LEFT JOIN formations f ON e.formation_id = f.id
        WHERE n.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $note = $stmt->get_result()->fetch_assoc();

    if (!$note) {
        header('Location: ../dashboard.php');
        exit();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $nouvelle_note = (float)$_POST['note'];

    $stmt = $conn->prepare("UPDATE notes SET note = ? WHERE id = ?");
    $stmt->bind_param("di", $nouvelle_note, $id);
    
    if ($stmt->execute()) {
        $success = "La note a été mise à jour avec succès";
        
        // Mise à jour des données affichées
        $stmt = $conn->prepare("
            SELECT n.*, 
                   e.nom as etudiant_nom, e.prenom as etudiant_prenom, e.matricule,
                   m.code as matiere_code, m.libelle as matiere_nom,
                   f.libelle as formation_nom
            FROM notes n
            JOIN etudiants e ON n.etudiant_id = e.id
            JOIN matieres m ON n.matiere_id = m.id
            LEFT JOIN formations f ON e.formation_id = f.id
            WHERE n.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $note = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Erreur lors de la mise à jour de la note";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Note - Gestion des Notes</title>
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
                                <i class="fas fa-edit text-primary"></i>
                                Modifier une Note
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

                            <!-- Informations de l'étudiant -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-graduate text-primary me-2"></i>
                                        Informations de l'étudiant
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Nom :</strong> 
                                        <?php echo isset($note['etudiant_nom']) ? htmlspecialchars($note['etudiant_nom']) : '-'; ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Prénom :</strong> 
                                        <?php echo isset($note['etudiant_prenom']) ? htmlspecialchars($note['etudiant_prenom']) : '-'; ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Matricule :</strong> 
                                        <?php echo isset($note['matricule']) ? htmlspecialchars($note['matricule']) : '-'; ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Formation :</strong> 
                                        <?php echo isset($note['formation_nom']) ? htmlspecialchars($note['formation_nom']) : '-'; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Informations de la matière -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-book text-primary me-2"></i>
                                        Informations de la matière
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Code :</strong> 
                                        <?php echo isset($note['matiere_code']) ? htmlspecialchars($note['matiere_code']) : '-'; ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Libellé :</strong> 
                                        <?php echo isset($note['matiere_nom']) ? htmlspecialchars($note['matiere_nom']) : '-'; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Formulaire de modification -->
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $note['id']; ?>">
                                
                                <div class="mb-4">
                                    <label for="note" class="form-label">
                                        <i class="fas fa-star me-2"></i>Note sur 20
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="note" 
                                           name="note" 
                                           value="<?php echo $note['note']; ?>"
                                           min="0" 
                                           max="20" 
                                           step="0.25" 
                                           required>
                                    <div class="invalid-feedback">
                                        Veuillez entrer une note valide entre 0 et 20
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>
                                        Enregistrer la modification
                                    </button>
                                    <a href="../etudiants/voir_notes.php?id=<?php echo $note['etudiant_id']; ?>" 
                                       class="btn btn-light btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Retour aux notes de l'étudiant
                                    </a>
                                    <a href="selection_etudiant.php" class="btn btn-secondary btn-lg mt-2">
                                        <i class="fas fa-users me-2"></i>
                                        Retour à la sélection
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
