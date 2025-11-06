<?php
session_start();
require 'config/database.php';
require 'config/csrf.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token()) {
        $email = trim($_POST['email']);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $conn = db_connect();
            
            // Vérifier si l'email existe
            $stmt = $conn->prepare('SELECT id, username FROM users WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Générer un token sécurisé
                if (function_exists('random_bytes')) {
                    $token = bin2hex(random_bytes(32));
                } elseif (function_exists('openssl_random_pseudo_bytes')) {
                    $token = bin2hex(openssl_random_pseudo_bytes(32));
                } else {
                    $token = bin2hex(uniqid(mt_rand(), true));
                }
                
                // Définir l'expiration (1 heure)
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Supprimer les anciens tokens de cet utilisateur
                $delete_stmt = $conn->prepare('DELETE FROM password_reset_tokens WHERE user_id = ?');
                $delete_stmt->bind_param('i', $user['id']);
                $delete_stmt->execute();
                $delete_stmt->close();
                
                // Insérer le nouveau token
                $insert_stmt = $conn->prepare('INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)');
                $insert_stmt->bind_param('iss', $user['id'], $token, $expires_at);
                $insert_stmt->execute();
                $insert_stmt->close();
                
                // Dans un environnement de production, envoyez un email ici
                // Pour le développement, on affiche le lien
                $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/reset_password.php?token=' . $token;
                
                // TODO: Envoyer l'email
                // mail($email, "Réinitialisation de mot de passe - SkillAfrik", "Cliquez sur ce lien pour réinitialiser votre mot de passe: $reset_link");
                
                $success = true;
                $success_message = "Un lien de réinitialisation a été envoyé à votre adresse email.";
                
                // En développement, afficher le lien
                if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
                    $success_message .= "<br><br><strong>Mode développement:</strong><br><a href='$reset_link'>$reset_link</a>";
                }
            } else {
                // Pour des raisons de sécurité, on affiche le même message
                $success = true;
                $success_message = "Si cette adresse email existe, un lien de réinitialisation a été envoyé.";
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $error = "Adresse email invalide.";
        }
    } else {
        $error = "Token CSRF invalide.";
    }
}

include 'views/partials/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Mot de passe oublié</h1>
        <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
            <p><a href="login.php">Retour à la connexion</a></p>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="votre@email.com">
                </div>

                <?php csrf_input(); ?>

                <button type="submit" class="btn-primary btn-block">Envoyer le lien</button>
            </form>

            <p class="auth-links">
                <a href="login.php">Retour à la connexion</a> | 
                <a href="register.php">Créer un compte</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
