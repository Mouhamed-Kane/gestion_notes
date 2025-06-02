<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$success = $error = '';

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $libelle = trim($_POST['libelle']);
    $coefficient = (float)$_POST['coefficient'];

    // Vérification si le code existe déjà
    $check = $conn->prepare("SELECT id FROM matieres WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $error = "Une matière avec ce code existe déjà";
    } else {
        $stmt = $conn->prepare("INSERT INTO matieres (code, libelle, coefficient) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $code, $libelle, $coefficient);
        
        if ($stmt->execute()) {
            $success = "La matière a été ajoutée avec succès";
        } else {
            $error = "Erreur lors de l'ajout de la matière";
        }
    }
}

// Suppression d'une matière
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Vérification si la matière a des notes
    $check = $conn->prepare("SELECT id FROM notes WHERE matiere_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $error = "Impossible de supprimer cette matière car elle contient des notes";
    } else {
        $stmt = $conn->prepare("DELETE FROM matieres WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = "La matière a été supprimée avec succès";
        } else {
            $error = "Erreur lors de la suppression de la matière";
        }
    }
}

// Récupération des matières avec le nombre de notes
$matieres = $conn->query("
    SELECT m.*, 
           COUNT(n.id) as nb_notes,
           AVG(n.note) as moyenne_generale
    FROM matieres m 
    LEFT JOIN notes n ON m.id = n.matiere_id 
    GROUP BY m.id 
    ORDER BY m.code
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Matières - Gestion des Notes</title>
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
                        <a class="nav-link active" href="liste.php">
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
            <div class="row">
                <!-- Formulaire d'ajout -->
                <div class="col-md-4">
                    <div class="card fade-in mb-4">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">
                                <i class="fas fa-plus-circle text-primary"></i>
                                Nouvelle Matière
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
                                    <label for="code" class="form-label">
                                        <i class="fas fa-hashtag me-2"></i>Code
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="code" 
                                           name="code" 
                                           required 
                                           pattern="[A-Za-z0-9-_]+"
                                           maxlength="10">
                                    <div class="invalid-feedback">
                                        Le code est requis et ne doit contenir que des lettres, chiffres, tirets et underscores
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="libelle" class="form-label">
                                        <i class="fas fa-font me-2"></i>Libellé
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="libelle" 
                                           name="libelle" 
                                           required>
                                    <div class="invalid-feedback">
                                        Le libellé est requis
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="coefficient" class="form-label">
                                        <i class="fas fa-balance-scale me-2"></i>Coefficient
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="coefficient" 
                                           name="coefficient" 
                                           step="0.5"
                                           min="0.5"
                                           value="1"
                                           required>
                                    <div class="invalid-feedback">
                                        Le coefficient doit être supérieur à 0
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Ajouter la matière
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Liste des matières -->
                <div class="col-md-8">
                    <div class="card fade-in">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">
                                <i class="fas fa-list text-primary"></i>
                                Liste des Matières
                            </h2>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Libellé</th>
                                            <th class="text-center">Coefficient</th>
                                            <th class="text-center">Notes</th>
                                            <th class="text-center">Moyenne</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($matieres->num_rows > 0): ?>
                                            <?php while ($matiere = $matieres->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($matiere['code']); ?></td>
                                                    <td><?php echo htmlspecialchars($matiere['libelle']); ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">
                                                            ×<?php echo $matiere['coefficient']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">
                                                            <?php echo $matiere['nb_notes']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($matiere['moyenne_generale']): ?>
                                                            <span class="badge bg-<?php echo $matiere['moyenne_generale'] >= 10 ? 'success' : 'danger'; ?>">
                                                                <?php echo number_format($matiere['moyenne_generale'], 2); ?>/20
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="modifier.php?id=<?php echo $matiere['id']; ?>" 
                                                           class="btn btn-sm btn-primary me-1">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($matiere['nb_notes'] == 0): ?>
                                                            <a href="?action=delete&id=<?php echo $matiere['id']; ?>" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    Aucune matière enregistrée
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
