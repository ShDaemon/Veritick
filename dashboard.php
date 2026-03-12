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

require_once 'includes/header.php'; 
?>
<div class="card" style="margin-top: 2rem;">
    <h2>Welcome to your Dashboard, <span style="color: var(--green);"><?= htmlspecialchars($name) ?></span>!</h2>
    <p class="text-muted" style="font-size: 1.1rem;">You are logged in as a <strong style="color: var(--paprika);"><?= ucfirst(htmlspecialchars($role)) ?></strong>.</p>
    <div style="margin-top: 30px; display: flex; flex-wrap: wrap; gap: 15px;">
        <?php if ($role === 'admin'): ?>
            <a href="create_event.php" class="btn btn-primary" style="width: auto;">+ Create New Event</a>
            <a href="checkin.php" class="btn btn-secondary" style="width: auto;">Scanner</a>
            <a href="my_events.php" class="btn btn-outline" style="width: auto;">Manage My Events</a>
        <?php else: ?>
            <a href="index.php" class="btn btn-primary" style="width: auto;">Browse Events</a>
            <a href="my_tickets.php" class="btn btn-outline" style="width: auto;">View My Tickets</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>