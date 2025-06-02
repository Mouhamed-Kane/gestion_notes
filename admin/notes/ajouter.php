<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';

// Récupération de l'étudiant si spécifié dans l'URL
$etudiant_preselectionne = null;
if (isset($_GET['etudiant_id'])) {
    $stmt = $conn->prepare("
        SELECT e.*, f.libelle as formation_nom 
        FROM etudiants e 
        LEFT JOIN formations f ON e.formation_id = f.id 
        WHERE e.id = ?
    ");
    $stmt->bind_param("i", $_GET['etudiant_id']);
    $stmt->execute();
    $etudiant_preselectionne = $stmt->get_result()->fetch_assoc();
}

// Récupération des étudiants pour le select
$etudiants = $conn->query("
    SELECT e.*, f.libelle as formation_nom 
    FROM etudiants e
    LEFT JOIN formations f ON e.formation_id = f.id 
    ORDER BY e.nom, e.prenom
");

// Récupération des matières
$matieres = $conn->query("SELECT * FROM matieres ORDER BY libelle");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etudiant_id = (int)$_POST['etudiant_id'];
    $matiere_id = (int)$_POST['matiere_id'];
    $note = (float)$_POST['note'];

    // Vérification si la note existe déjà
    $check = $conn->prepare("SELECT id FROM notes WHERE etudiant_id = ? AND matiere_id = ?");
    $check->bind_param("ii", $etudiant_id, $matiere_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $error = "Une note existe déjà pour cet étudiant dans cette matière";
    } else {
        $stmt = $conn->prepare("INSERT INTO notes (etudiant_id, matiere_id, note) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $etudiant_id, $matiere_id, $note);
        
        if ($stmt->execute()) {
            $success = "La note a été ajoutée avec succès";
            // Redirection vers la page des notes de l'étudiant
            header("Location: ../etudiants/voir_notes.php?id=" . $etudiant_id);
            exit();
        } else {
            $error = "Erreur lors de l'ajout de la note";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Note - Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                <i class="fas fa-plus-circle text-primary"></i>
                                Ajouter une Note
                            </h1>

                            <?php if ($error): ?>
                                <div class="alert alert-danger fade-in">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="etudiant_id" class="form-label">
                                        <i class="fas fa-user-graduate me-2"></i>Étudiant
                                    </label>
                                    <select class="form-select select2" id="etudiant_id" name="etudiant_id" required>
                                        <option value="">Sélectionner un étudiant</option>
                                        <?php while ($etudiant = $etudiants->fetch_assoc()): ?>
                                            <option value="<?php echo $etudiant['id']; ?>"
                                                    <?php echo ($etudiant_preselectionne && $etudiant_preselectionne['id'] == $etudiant['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom'] . 
                                                                         ' (' . $etudiant['matricule'] . ') - ' . 
                                                                         $etudiant['formation_nom']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner un étudiant</div>
                                </div>

                                <div class="mb-4">
                                    <label for="matiere_id" class="form-label">
                                        <i class="fas fa-book me-2"></i>Matière
                                    </label>
                                    <select class="form-select select2" id="matiere_id" name="matiere_id" required>
                                        <option value="">Sélectionner une matière</option>
                                        <?php while ($matiere = $matieres->fetch_assoc()): ?>
                                            <option value="<?php echo $matiere['id']; ?>">
                                                <?php echo htmlspecialchars($matiere['code'] . ' - ' . $matiere['libelle']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner une matière</div>
                                </div>

                                <div class="mb-4">
                                    <label for="note" class="form-label">
                                        <i class="fas fa-star me-2"></i>Note sur 20
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="note" 
                                           name="note" 
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
                                        Enregistrer la note
                                    </button>
                                    <?php if ($etudiant_preselectionne): ?>
                                        <a href="../etudiants/voir_notes.php?id=<?php echo $etudiant_preselectionne['id']; ?>" 
                                           class="btn btn-light btn-lg">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Retour aux notes de l'étudiant
                                        </a>
                                    <?php else: ?>
                                        <a href="../dashboard.php" class="btn btn-light btn-lg">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Retour au tableau de bord
                                        </a>
                                    <?php endif; ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Initialisation de Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

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
