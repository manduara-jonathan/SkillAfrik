<?php
/**
 * Gestion des Administrateurs
 * 
 * Seul le super-administrateur peut accéder à cette page
 * Permet de promouvoir/rétrograder des utilisateurs en admin
 * 
 * Auteur: Jonathan Manduara Tshimpaka
 * Email: manduarajonathan.m@gmail.com
 * Téléphone: +243890868095
 */

require 'superadmin_check.php';
require '../config/database.php';
require '../config/csrf.php';

$conn = db_connect();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        die('Token CSRF invalide');
    }
    
    $action = $_POST['action'] ?? '';
    $user_id = (int)($_POST['user_id'] ?? 0);
    
    if ($user_id > 0) {
        if ($action === 'promote_admin') {
            // Promouvoir en admin
            $stmt = $conn->prepare('UPDATE users SET role = ? WHERE id = ? AND role != ?');
            $role = 'admin';
            $superadmin = 'superadmin';
            $stmt->bind_param('sis', $role, $user_id, $superadmin);
            $stmt->execute();
            $message = "Utilisateur promu en administrateur avec succès.";
        } elseif ($action === 'demote_user') {
            // Rétrograder en utilisateur
            $stmt = $conn->prepare('UPDATE users SET role = ? WHERE id = ? AND role != ?');
            $role = 'apprenant';
            $superadmin = 'superadmin';
            $stmt->bind_param('sis', $role, $user_id, $superadmin);
            $stmt->execute();
            $message = "Utilisateur rétrogradé avec succès.";
        }
    }
}

// Récupérer tous les utilisateurs
$users_query = "SELECT id, username, first_name, last_name, email, phone, role, created_at, last_login 
                FROM users 
                ORDER BY 
                    CASE role 
                        WHEN 'superadmin' THEN 1 
                        WHEN 'admin' THEN 2 
                        WHEN 'formateur' THEN 3 
                        ELSE 4 
                    END, 
                    created_at DESC";
$users_result = $conn->query($users_query);

$pageTitle = "Gestion des Administrateurs";
include 'partials/header.php';
?>

<div class="container">
    <h1>👑 Gestion des Administrateurs</h1>
    <p class="subtitle">Gérer les rôles et permissions des utilisateurs</p>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h2>Liste des Utilisateurs</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                    <th>Dernière connexion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                        <td>
                            <?php 
                            $role_badges = [
                                'superadmin' => '<span class="badge badge-gold">👑 Super Admin</span>',
                                'admin' => '<span class="badge badge-danger">🛡️ Admin</span>',
                                'formateur' => '<span class="badge badge-info">👨‍🏫 Formateur</span>',
                                'apprenant' => '<span class="badge badge-secondary">👤 Apprenant</span>'
                            ];
                            echo $role_badges[$user['role']] ?? $user['role'];
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais'; ?></td>
                        <td>
                            <?php if ($user['role'] !== 'superadmin'): ?>
                                <form method="POST" style="display: inline;">
                                    <?php csrf_input(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <button type="submit" name="action" value="promote_admin" 
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Promouvoir cet utilisateur en administrateur ?')">
                                            ⬆️ Promouvoir Admin
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="demote_user" 
                                                class="btn btn-sm btn-warning"
                                                onclick="return confirm('Rétrograder cet administrateur en utilisateur ?')">
                                            ⬇️ Rétrograder
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">🔒 Protégé</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card" style="margin-top: 20px; background: #fff3cd; border-color: #ffc107;">
        <h3>ℹ️ Informations importantes</h3>
        <ul>
            <li><strong>Super Administrateur</strong> : Rôle le plus élevé, ne peut pas être modifié ou supprimé</li>
            <li><strong>Administrateur</strong> : Peut gérer les cours, modules, leçons et quiz</li>
            <li><strong>Formateur</strong> : Peut créer et gérer ses propres cours</li>
            <li><strong>Apprenant</strong> : Utilisateur standard qui suit les cours</li>
        </ul>
        
        <p><strong>👑 Super Administrateur actuel :</strong> Jonathan Manduara Tshimpaka</p>
        <p><strong>📧 Contact :</strong> manduarajonathan.m@gmail.com | +243890868095</p>
    </div>
</div>

<style>
.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}
.badge-gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000;
}
.badge-danger {
    background: #dc3545;
    color: white;
}
.badge-info {
    background: #17a2b8;
    color: white;
}
.badge-secondary {
    background: #6c757d;
    color: white;
}
.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}
.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
}
</style>

<?php
$conn->close();
include 'partials/footer.php';
?>
