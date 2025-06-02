<?php
session_start();
require_once '../../db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.php?type=admin');
    exit();
}

$etudiant = null;
$notes = [];
$moyenne_generale = 0;

// Récupération de l'étudiant et ses notes
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Récupération des informations de l'étudiant
    $stmt = $conn->prepare("
        SELECT e.*, f.libelle as formation_nom 
        FROM etudiants e 
        LEFT JOIN formations f ON e.formation_id = f.id 
        WHERE e.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $etudiant = $stmt->get_result()->fetch_assoc();

    if ($etudiant) {
        // Récupération des notes par matière
        $stmt = $conn->prepare("
            SELECT n.*, m.code as matiere_code, m.libelle as matiere_nom
            FROM notes n
            JOIN matieres m ON n.matiere_id = m.id
            WHERE n.etudiant_id = ?
            ORDER BY m.libelle
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $total_notes = 0;
        $nombre_notes = 0;

        while ($note = $result->fetch_assoc()) {
            $notes[] = $note;
            $total_notes += $note['note'];
            $nombre_notes++;
        }

        // Calcul de la moyenne générale
        if ($nombre_notes > 0) {
            $moyenne_generale = round($total_notes / $nombre_notes, 2);
        }
    } else {
        header('Location: liste.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes de l'Étudiant - Gestion des Notes</title>
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
                <div class="col-md-10">
                    <div class="card fade-in">
                        <div class="card-body">
                            <!-- Informations de l'étudiant -->
                            <div class="text-center mb-4">
                                <h1 class="card-title">
                                    <i class="fas fa-user-graduate text-primary me-2"></i>
                                    <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>
                                </h1>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-id-card me-2"></i>
                                    Matricule: <?php echo htmlspecialchars($etudiant['matricule']); ?>
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Formation: <?php echo htmlspecialchars($etudiant['formation_nom']); ?>
                                </p>
                            </div>

                            <!-- Carte de la moyenne générale -->
                            <div class="row mb-4">
                                <div class="col-md-6 mx-auto">
                                    <div class="stat-card text-center">
                                        <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                                        <h3>Moyenne Générale</h3>
                                        <h2 class="display-4 <?php echo $moyenne_generale >= 10 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $moyenne_generale; ?>/20
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau des notes -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Matière</th>
                                            <th class="text-center">Note/20</th>
                                            <th class="text-center">Mention</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($notes) > 0): ?>
                                            <?php foreach ($notes as $note): ?>
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
                                                    <td class="text-center">
                                                        <a href="../notes/modifier.php?id=<?php echo $note['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    Aucune note enregistrée pour cet étudiant
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-grid gap-2 mt-4">
                                <a href="../notes/ajouter.php?etudiant_id=<?php echo $etudiant['id']; ?>" 
                                   class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Ajouter une note
                                </a>
                                <a href="modifier.php?id=<?php echo $etudiant['id']; ?>" 
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-edit me-2"></i>
                                    Modifier les informations
                                </a>
                                <a href="liste.php" class="btn btn-light btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
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
