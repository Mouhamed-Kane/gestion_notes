<?php
session_start();
require_once 'db.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'etudiant';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sécuriser et valider les entrées
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = $_POST['password'];

    // Vérification de l'email ou matricule
    if (empty($login) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Préparer la requête selon le type
        if ($type == 'admin') {
            // Connexion administrateur par email
            $sql = "SELECT * FROM utilisateurs WHERE email = ? AND role = 'administrateur'";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['mot_de_passe'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = 'admin';
                        header("Location: admin/dashboard.php");
                        exit();
                    } else {
                        $error = "Mot de passe incorrect.";
                    }
                } else {
                    $error = "Identifiant non trouvé.";
                }
            } else {
                $error = "Erreur lors de la préparation de la requête.";
            }
        } else {
            // Connexion étudiant par matricule
            $sql = "SELECT * FROM etudiants WHERE matricule = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['mot_de_passe'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = 'etudiant';
                        $_SESSION['matricule'] = $user['matricule'];
                        header("Location: etudiant/dashboard.php");
                        exit();
                    } else {
                        $error = "Mot de passe incorrect.";
                    }
                } else {
                    $error = "Identifiant non trouvé.";
                }
            } else {
                $error = "Erreur lors de la préparation de la requête.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo ucfirst($type); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="auth-container fade-in">
                        <div class="card p-4">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <i class="fas <?php echo $type == 'admin' ? 'fa-user-shield' : 'fa-user-graduate'; ?> fa-3x text-primary mb-3"></i>
                                    <h2 class="page-title">Connexion <?php echo ucfirst($type); ?></h2>
                                </div>

                                <?php if ($error): ?>
                                    <div class="alert alert-danger fade-in">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="" class="fade-in">
                                    <div class="mb-4">
                                        <label for="login" class="form-label">
                                            <i class="fas <?php echo $type == 'admin' ? 'fa-envelope' : 'fa-id-card'; ?> me-2"></i>
                                            <?php echo $type == 'admin' ? 'Email' : 'Matricule'; ?>
                                        </label>
                                        <input type="<?php echo $type == 'admin' ? 'email' : 'text'; ?>" 
                                               class="form-control form-control-lg" 
                                               id="login" 
                                               name="login" 
                                               required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>
                                            Mot de passe
                                        </label>
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="password" 
                                               name="password" 
                                               required>
                                    </div>
                                    <div class="d-grid gap-3">
                                        <button type="submit" class="btn btn-primary btn-lg btn-custom">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            Se connecter
                                        </button>
                                        <a href="index.php" class="btn btn-light btn-lg btn-custom">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Retour à l'accueil
                                        </a>
                                    </div>
                                </form>
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
