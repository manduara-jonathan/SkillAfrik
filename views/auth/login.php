<div class="auth-container">
    <div class="auth-form">
        <h2>Connexion</h2>
        <p>Accédez à votre tableau de bord.</p>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success">
                <p><?php echo $_SESSION['success_message']; ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <small style="float: right;"><a href="forgot_password.php">Mot de passe oublié ?</a></small>
            </div>
            <?php csrf_input(); ?>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
        <p class="auth-switch">Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>
</div>
