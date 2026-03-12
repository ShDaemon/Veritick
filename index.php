<?php 
require_once 'includes/db.php';
require_once 'includes/header.php'; 

$stmt = $pdo->query("SELECT * FROM Events WHERE date >= NOW() ORDER BY date ASC");
$events = $stmt->fetchAll();
?>
<div style="text-align: center; margin-bottom: 3rem; padding: 2rem 0;">
    <h1 style="font-size: 3.5rem; margin-bottom: 10px; color: var(--mocha);">Experience the <span class="text-accent">Future</span></h1>
    <p class="text-muted" style="font-size: 1.2rem;">Secure, fast, and digital ticketing.</p>
</div>
<div class="grid">
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="card">
                <h3 style="color: var(--mocha); font-size: 1.5rem;"><?= htmlspecialchars($event['title']) ?></h3>
                <p style="margin-bottom: 10px; font-weight: 600;"><span class="text-paprika">📅</span> <?= date('M j, Y • g:i a', strtotime($event['date'])) ?></p>
                <p class="text-muted" style="margin-bottom: 25px;"><strong>📍</strong> <?= htmlspecialchars($event['location']) ?></p>
                <a href="event_details.php?id=<?= $event['event_id'] ?>" class="btn btn-primary" style="text-decoration: none;">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center;">
            <p class="text-muted">No upcoming events right now. Check back soon!</p>
        </div>
    <?php endif; ?>
</div>
</div>
</body>
</html>