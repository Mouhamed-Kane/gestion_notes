<?php
session_start();
require_once '../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'etudiant') {
    header('Location: ../login.php');
    exit();
}

// Récupération des informations de l'étudiant
$etudiant_id = $_SESSION['user_id'];
$etudiant = $conn->query("SELECT e.*, f.libelle as formation_nom FROM etudiants e LEFT JOIN formations f ON e.formation_id = f.id WHERE e.id = $etudiant_id")->fetch_assoc();

// Récupération des notes
$notes = $conn->query("SELECT n.*, m.libelle as matiere_nom, m.code as matiere_code FROM notes n JOIN matieres m ON n.matiere_id = m.id WHERE n.etudiant_id = $etudiant_id");
$nb_notes = $notes->num_rows;
$moyenne = null;
if ($nb_notes > 0) {
    $somme = 0;
    while ($n = $notes->fetch_assoc()) {
        $somme += $n['note'];
    }
    $moyenne = round($somme / $nb_notes, 2);
}
// On relance la requête pour l'affichage du tableau
$notes = $conn->query("SELECT n.*, m.libelle as matiere_nom, m.code as matiere_code FROM notes n JOIN matieres m ON n.matiere_id = m.id WHERE n.etudiant_id = $etudiant_id");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant - Gestion des Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="container py-5">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <div class="card fade-in">
                        <div class="card-body">
                            <h1 class="card-title text-center mb-3">
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                Bonjour, <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?> !
                            </h1>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Matricule :</strong> <?php echo htmlspecialchars($etudiant['matricule']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Formation :</strong> <?php echo htmlspecialchars($etudiant['formation_nom']); ?></p>
                                </div>
                            </div>
                            <div class="row text-center mb-4">
                                <div class="col-md-6 mb-2">
                                    <div class="stat-card">
                                        <i class="fas fa-book-open fa-2x text-primary mb-2"></i>
                                        <h4>Nombre de notes</h4>
                                        <span class="badge bg-info fs-5"><?php echo $nb_notes; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="stat-card">
                                        <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                                        <h4>Moyenne générale</h4>
                                        <span class="badge bg-<?php echo ($moyenne !== null && $moyenne >= 10) ? 'success' : 'danger'; ?> fs-5">
                                            <?php echo $moyenne !== null ? $moyenne : '-'; ?>/20
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mb-3">
                                <a href="#notes" class="btn btn-primary btn-lg">
                                    <i class="fas fa-list me-2"></i> Consulter mes notes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tableau des notes -->
            <div class="row justify-content-center" id="notes">
                <div class="col-md-10">
                    <div class="card fade-in">
                        <div class="card-body">
                            <h2 class="card-title mb-4">
                                <i class="fas fa-list text-primary me-2"></i> Mes Notes
                            </h2>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Matière</th>
                                            <th class="text-center">Note / 20</th>
                                            <th class="text-center">Mention</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($notes->num_rows > 0): ?>
                                            <?php while ($note = $notes->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($note['matiere_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($note['matiere_nom']); ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?php echo $note['note'] >= 10 ? 'success' : 'danger'; ?> p-2">
                                                            <?php echo $note['note']; ?>/20
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($note['note'] >= 16) echo '<span class="text-success">Très Bien</span>';
                                                        elseif ($note['note'] >= 14) echo '<span class="text-primary">Bien</span>';
                                                        elseif ($note['note'] >= 12) echo '<span class="text-info">Assez Bien</span>';
                                                        elseif ($note['note'] >= 10) echo '<span class="text-warning">Passable</span>';
                                                        else echo '<span class="text-danger">Insuffisant</span>';
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Aucune note enregistrée</td>
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
</body>
</html>
