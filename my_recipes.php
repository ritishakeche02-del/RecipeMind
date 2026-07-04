<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_helpers.php';
requireLogin();
$user = currentUser();

$stmt = $pdo->prepare('SELECT id, title, description, status, created_at FROM recipe_requests WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$requests = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>
<div class="page-hero fade-in">
    <h1>👨‍🍳 My Recipes</h1>
    <p style="font-size:1rem;color:var(--t2);max-width:500px;margin:0 auto;">
        Track the status of your submitted recipe requests
    </p>
</div>

<?php if (empty($requests)): ?>
    <div class="empty-state fade-in fade-in-delay-1">
        <span class="empty-icon">📝</span>
        <h3>You haven't submitted any recipes yet</h3>
        <p>Share your culinary creations with the community!</p>
        <a href="new_recipe_request.php" class="btn btn-accent">Submit a Recipe</a>
    </div>
<?php else: ?>
    <div class="card fade-in fade-in-delay-1" style="padding:0;overflow:hidden;">
        <table>
            <thead>
                <tr>
                    <th>Recipe Title</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($req['title']) ?></td>
                        <td style="color:var(--t3);"><?= htmlspecialchars(date('M j, Y', strtotime($req['created_at']))) ?></td>
                        <td>
                            <?php if ($req['status'] === 'published' || $req['status'] === 'approved'): ?>
                                <span class="status-badge status-published">✓ Published</span>
                            <?php elseif ($req['status'] === 'rejected' || $req['status'] === 'denied'): ?>
                                <span class="status-badge status-rejected">✗ Rejected</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">⏳ Pending Review</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php';
