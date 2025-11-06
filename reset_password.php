<?php
session_start();
require 'config/database.php';
require 'config/csrf.php';

$error = '';
$success = false;
$valid_token = false;
$token = '';

// Vérifier le token
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    $conn = db_connect();
    $stmt = $conn->prepare('SELECT prt.*, u.email 
                            FROM password_reset_tokens prt 
                            JOIN users u ON prt.user_id = u.id 
                            WHERE prt.token = ? AND prt.used = 0 AND prt.expires_at > NOW()');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid_token = true;
        $token_data = $result->fetch_assoc();
    } else {
        $error = "Ce lien de réinitialisation est invalide ou a expiré.";
    }
    
    $stmt->close();
    $conn->close();
} else {
    $error = "Token manquant.";
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    if (validate_csrf_token()) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (strlen($password) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères.";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $conn = db_connect();
            
            // Hasher le nouveau mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe
            $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->bind_param('si', $hashed_password, $token_data['user_id']);
            $stmt->execute();
            $stmt->close();
            
            // Marquer le token comme utilisé
            $stmt = $conn->prepare('UPDATE password_reset_tokens SET used = 1 WHERE token = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->close();
            
            $conn->close();
            
            $success = true;
        }
    } else {
        $error = "Token CSRF invalide.";
    }
}

include 'views/partials/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Réinitialiser le mot de passe</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Votre mot de passe a été réinitialisé avec succès !
            </div>
            <p><a href="login.php" class="btn-primary btn-block">Se connecter</a></p>
        <?php elseif (!$valid_token): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <p><a href="forgot_password.php">Demander un nouveau lien</a></p>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <p>Entrez votre nouveau mot de passe pour le compte : <strong><?php echo htmlspecialchars($token_data['email']); ?></strong></p>

            <form action="" method="POST" class="auth-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required 
                           minlength="6" placeholder="Au moins 6 caractères">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           minlength="6" placeholder="Retapez le mot de passe">
                </div>

                <?php csrf_input(); ?>

                <button type="submit" class="btn-primary btn-block">Réinitialiser le mot de passe</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
