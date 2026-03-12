<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$organizer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT e.*, 
           (SELECT COUNT(*) FROM Tickets t WHERE t.event_id = e.event_id) AS tickets_sold,
           (SELECT COUNT(*) FROM Tickets t WHERE t.event_id = e.event_id AND t.used = 1) AS tickets_scanned
    FROM Events e 
    WHERE e.organizer_id = ? 
    ORDER BY e.date DESC
");
$stmt->execute([$organizer_id]);
$my_events = $stmt->fetchAll();

require_once 'includes/header.php'; 
?>
<div style="max-width: 1000px; margin: 2rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #D1D1D1; padding-bottom: 15px;">
        <h2 style="color: var(--mocha); margin: 0;">My Organized Events</h2>
        <a href="create_event.php" class="btn btn-primary" style="width: auto; padding: 10px 20px;">+ New Event</a>
    </div>
    <div style="display: flex; flex-direction: column; gap: 25px;">
        <?php if (count($my_events) > 0): ?>
            <?php foreach ($my_events as $event): ?>
                <?php 
                    $sold_percent = ($event['total_seats'] > 0) ? round(($event['tickets_sold'] / $event['total_seats']) * 100) : 0;
                    $scanned_percent = ($event['tickets_sold'] > 0) ? round(($event['tickets_scanned'] / $event['tickets_sold']) * 100) : 0;
                    $is_past = strtotime($event['date']) < time();
                ?>
                <div class="card" style="display: flex; flex-wrap: wrap; gap: 25px; align-items: center; <?= $is_past ? 'opacity: 0.6;' : '' ?>">
                    <div style="flex: 2; min-width: 250px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                            <h3 style="color: var(--mocha); margin: 0; font-size: 1.6rem;"><?= htmlspecialchars($event['title']) ?></h3>
                            <?php if ($is_past): ?>
                                <span class="badge badge-neutral">Past</span>
                            <?php else: ?>
                                <span class="badge badge-success">Upcoming</span>
                            <?php endif; ?>
                        </div>
                        <p style="margin-bottom: 5px; font-weight: 600;"><span style="color: var(--paprika);">📅</span> <?= date('F j, Y, g:i a', strtotime($event['date'])) ?></p>
                        <p class="text-muted" style="font-weight: 600;"><span style="color: var(--paprika);">📍</span> <?= htmlspecialchars($event['location']) ?></p>
                    </div>
                    <div style="flex: 2; min-width: 300px; background: var(--smoke); padding: 20px; border-radius: 8px; border: 1px solid #D1D1D1;">
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem;">
                                <span style="color: var(--mocha); font-weight: 700;">Tickets Claimed</span>
                                <strong><?= $event['tickets_sold'] ?> / <?= $event['total_seats'] ?> (<span style="color: var(--green);"><?= $sold_percent ?>%</span>)</strong>
                            </div>
                            <div style="width: 100%; background: #E0E0E0; height: 10px; border-radius: 5px; overflow: hidden;">
                                <div style="width: <?= $sold_percent ?>%; background: var(--green); height: 100%; border-radius: 5px;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem;">
                                <span style="color: var(--mocha); font-weight: 700;">Guests Checked In</span>
                                <strong><?= $event['tickets_scanned'] ?> / <?= $event['tickets_sold'] ?> (<span style="color: var(--paprika);"><?= $scanned_percent ?>%</span>)</strong>
                            </div>
                            <div style="width: 100%; background: #E0E0E0; height: 10px; border-radius: 5px; overflow: hidden;">
                                <div style="width: <?= $scanned_percent ?>%; background: var(--paprika); height: 100%; border-radius: 5px;"></div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 150px; display: flex; flex-direction: column; gap: 12px;">
                        <a href="event_details.php?id=<?= $event['event_id'] ?>" class="btn btn-outline" style="padding: 12px;">View Page</a>
                        <?php if (!$is_past): ?>
                            <a href="checkin.php" class="btn btn-secondary" style="padding: 12px;">Scanner</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card card-center" style="text-align: center;">
                <p class="text-muted" style="margin-bottom: 20px; font-weight: 600;">You haven't organized any events yet.</p>
                <a href="create_event.php" class="btn btn-primary" style="width: auto;">Create Your First Event</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>