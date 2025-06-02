<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

// Récupération de la liste des étudiants
$etudiants = $conn->query("SELECT id, matricule, nom, prenom FROM etudiants ORDER BY nom, prenom");

$notes = null;
$etudiant = null;
if (isset($_GET['etudiant_id']) && is_numeric($_GET['etudiant_id'])) {
    $etudiant_id = (int)$_GET['etudiant_id'];
    $etudiant = $conn->query("SELECT * FROM etudiants WHERE id = $etudiant_id")->fetch_assoc();
    $notes = $conn->query("
        SELECT n.*, m.libelle as matiere_nom, m.code as matiere_code
        FROM notes n
        JOIN matieres m ON n.matiere_id = m.id
        WHERE n.etudiant_id = $etudiant_id
        ORDER BY m.libelle
    ");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection d'un étudiant - Gestion des Notes</title>
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
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card fade-in mb-4">
                        <div class="card-body">
                            <h2 class="card-title mb-4">
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                Sélectionner un étudiant
                            </h2>
                            <form method="GET" action="" class="row g-3 align-items-center mb-0">
                                <div class="col-md-9">
                                    <select name="etudiant_id" class="form-select form-select-lg" required>
                                        <option value="">-- Choisir un étudiant --</option>
                                        <?php while ($e = $etudiants->fetch_assoc()): ?>
                                            <option value="<?php echo $e['id']; ?>" <?php echo (isset($etudiant) && $etudiant['id'] == $e['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($e['matricule'] . ' - ' . $e['nom'] . ' ' . $e['prenom']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i> Rechercher
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if ($etudiant): ?>
                        <div class="card fade-in">
                            <div class="card-body">
                                <h3 class="card-title mb-3">
                                    <i class="fas fa-list text-primary me-2"></i>
                                    Notes de <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>
                                </h3>
                                <?php if ($notes && $notes->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Matière</th>
                                                    <th class="text-center">Note / 20</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($n = $notes->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($n['matiere_code']); ?></td>
                                                        <td><?php echo htmlspecialchars($n['matiere_nom']); ?></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?php echo $n['note'] >= 10 ? 'success' : 'danger'; ?> p-2">
                                                                <?php echo $n['note']; ?>/20
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="modifier.php?id=<?php echo $n['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-edit"></i> Modifier
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">Aucune note enregistrée pour cet étudiant.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
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
</body>
</html>
