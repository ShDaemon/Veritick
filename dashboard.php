<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];

$organizer_code = null;
if ($role === 'admin') {
    $stmt = $pdo->prepare("SELECT organizer_code FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $organizer_code = $stmt->fetchColumn();
}

require_once 'includes/header.php'; 
?>
<div class="card" style="margin-top: 2rem; max-width: 800px; margin-left: auto; margin-right: auto; text-align: center;">
    <h2>Welcome to your Dashboard, <span class="text-accent"><?= htmlspecialchars($name) ?></span>!</h2>
    <p class="text-muted" style="font-size: 1.1rem;">You are logged in as a <strong class="badge badge-neutral" style="margin-left: 5px;"><?= ucfirst(htmlspecialchars($role)) ?></strong></p>
    <?php if ($role === 'admin' && $organizer_code): ?>
        <div style="margin-top: 25px; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 12px; display: inline-block;">
            <span class="text-muted" style="margin-right: 10px;">Your Organizer Code:</span>
            <strong style="color: var(--secondary); font-size: 1.3rem; letter-spacing: 2px; font-family: monospace;"><?= htmlspecialchars($organizer_code) ?></strong>
            <p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-muted);">Share this code with your target users so they can register and access your events.</p>
        </div>
    <?php endif; ?>
    <div style="margin-top: 40px; display: flex; justify-content: center; flex-wrap: wrap; gap: 20px;">
        <?php if ($role === 'admin'): ?>
            <a href="create_event.php" class="btn btn-primary" style="width: auto;"><span>➕</span> Create New Event</a>
            <a href="checkin.php" class="btn btn-secondary" style="width: auto;"><span>📷</span> Scanner</a>
            <a href="my_events.php" class="btn btn-outline" style="width: auto;"><span>📊</span> Manage My Events</a>
        <?php else: ?>
            <a href="index.php" class="btn btn-primary" style="width: auto;"><span>🎟️</span> Browse Events</a>
            <a href="my_tickets.php" class="btn btn-outline" style="width: auto;"><span>📱</span> View My Tickets</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>