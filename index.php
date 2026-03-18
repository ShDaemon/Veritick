<?php 
require_once 'includes/db.php';
require_once 'includes/header.php'; 

$events = [];
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM Events WHERE date >= NOW() AND organizer_id = ? ORDER BY date ASC");
        $stmt->execute([$_SESSION['user_id']]);
        $events = $stmt->fetchAll();
    } elseif (isset($_SESSION['linked_organizer_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM Events WHERE date >= NOW() AND organizer_id = ? ORDER BY date ASC");
        $stmt->execute([$_SESSION['linked_organizer_id']]);
        $events = $stmt->fetchAll();
    }
}
?>
<section class="hero">
    <h1>Experience the <span class="text-accent">Future</span></h1>
    <p>Secure, fast, and digital ticketing. Find your next experience and book instantly.</p>
</section>

<div class="grid" style="margin-top: 2rem;">
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="card" style="padding: 0; display: flex; flex-direction: column;">
                <div style="height: 160px; background-image: url('<?= htmlspecialchars($event['image_url'] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=800&h=400') ?>'); background-size: cover; background-position: center;"></div>
                <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 0.8rem; flex-grow: 1;"><?= htmlspecialchars($event['title']) ?></h3>
                    <div>
                        <div class="event-card-date">
                            <span>📅</span> <?= date('M j, Y • g:i a', strtotime($event['date'])) ?>
                        </div>
                        <div class="event-card-location">
                            <span>📍</span> <?= htmlspecialchars($event['location']) ?>
                        </div>
                        <a href="event_details.php?id=<?= $event['event_id'] ?>" class="btn btn-primary" style="margin-top: auto;">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p class="text-muted" style="font-size: 1.2rem;">Please log in or create an account with an Organizer Access Code to view your events.</p>
                <a href="login.php" class="btn btn-primary" style="margin-top: 15px; width: auto; display: inline-block;">Log In</a>
            <?php else: ?>
                <p class="text-muted" style="font-size: 1.2rem;">No upcoming events right now. Check back soon!</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</div>
</body>
</html>