<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

// Récupération des formations
$formations_query = "SELECT * FROM formations ORDER BY libelle";
$formations = $conn->query($formations_query);

// Récupération des étudiants si une formation est sélectionnée
$formation_id = isset($_GET['formation_id']) ? (int)$_GET['formation_id'] : 0;
$etudiants = null;

if ($formation_id) {
    $query = "SELECT e.*, f.libelle as formation 
              FROM etudiants e
              LEFT JOIN formations f ON e.formation_id = f.id 
              WHERE e.formation_id = ?
              ORDER BY e.nom, e.prenom";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $formation_id);
    $stmt->execute();
    $etudiants = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étudiants par Formation - Gestion des Notes</title>
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
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="page-title">
                        <i class="fas fa-users-class text-primary me-2"></i>
                        Étudiants par Formation
                    </h1>
                </div>
            </div>

            <div class="card fade-in">
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label for="formation_id" class="form-label">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Sélectionner une formation
                                </label>
                                <select name="formation_id" id="formation_id" class="form-select form-select-lg" onchange="this.form.submit()">
                                    <option value="">Choisir une formation</option>
                                    <?php while ($formation = $formations->fetch_assoc()): ?>
                                        <option value="<?php echo $formation['id']; ?>" 
                                                <?php echo $formation_id == $formation['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($formation['libelle']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <?php if ($formation_id && $etudiants): ?>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Téléphone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($etudiants->num_rows > 0): ?>
                                        <?php while ($etudiant = $etudiants->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($etudiant['matricule']); ?></td>
                                                <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                                <td><?php echo htmlspecialchars($etudiant['telephone']); ?></td>
                                                <td>
                                                    <a href="modifier.php?id=<?php echo $etudiant['id']; ?>" 
                                                       class="btn btn-sm btn-primary me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="voir_notes.php?id=<?php echo $etudiant['id']; ?>" 
                                                       class="btn btn-sm btn-info me-1">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Aucun étudiant trouvé dans cette formation
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif ($formation_id): ?>
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun étudiant n'est inscrit dans cette formation
                        </div>
                    <?php endif; ?>
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
