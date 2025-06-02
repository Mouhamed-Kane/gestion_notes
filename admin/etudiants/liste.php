<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Recherche
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = '';
if ($search) {
    $where = "WHERE e.matricule LIKE '%$search%' 
              OR e.nom LIKE '%$search%' 
              OR e.prenom LIKE '%$search%'
              OR f.libelle LIKE '%$search%'";
}

// Requête pour obtenir le nombre total d'étudiants
$count_query = "SELECT COUNT(*) as total FROM etudiants e 
                LEFT JOIN formations f ON e.formation_id = f.id 
                $where";
$total_result = $conn->query($count_query);
$total = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Requête pour obtenir les étudiants avec pagination
$query = "SELECT e.*, f.libelle as formation 
          FROM etudiants e
          LEFT JOIN formations f ON e.formation_id = f.id 
          $where
          ORDER BY e.nom, e.prenom 
          LIMIT $offset, $limit";
$result = $conn->query($query);

// Message de succès/erreur
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants - Gestion des Notes</title>
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
                        <a class="nav-link" href="ajouter.php">
                            <i class="fas fa-user-plus"></i> Ajouter un étudiant
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
                <div class="col-md-6">
                    <h1 class="page-title">
                        <i class="fas fa-users text-primary me-2"></i>
                        Liste des Étudiants
                    </h1>
                </div>
                <div class="col-md-6">
                    <form class="d-flex" method="GET">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="Rechercher un étudiant..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card fade-in">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Formation</th>
                                    <th>Téléphone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($etudiant = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($etudiant['matricule']); ?></td>
                                            <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                            <td><?php echo htmlspecialchars($etudiant['formation']); ?></td>
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
                                        <td colspan="6" class="text-center">Aucun étudiant trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
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
