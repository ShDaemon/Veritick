<?php
session_start();
require_once 'includes/db.php';

/* 1. Validate Event ID */
if (!isset($_GET['id'])) {
    require_once 'includes/header.php';
    echo "<div class='card card-center'>
            <div class='alert alert-error'>
                Invalid Event ID. <a href='index.php'>Go back</a>
            </div>
          </div></body></html>";
    exit;
}

$event_id = (int)$_GET['id'];

/* 2. Fetch event ONLY if not expired */
$stmt = $pdo->prepare("
    SELECT e.*, u.name AS organizer_name
    FROM Events e
    JOIN Users u ON e.organizer_id = u.user_id
    WHERE e.event_id = ?
    AND e.date >= NOW()
");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

/* 3. If event not found or expired */
if (!$event) {
    require_once 'includes/header.php';
    echo "<div class='card card-center'>
            <div class='alert alert-error'>
                This event does not exist or has already ended.
                <a href='index.php'>Browse upcoming events</a>
            </div>
          </div></body></html>";
    exit;
}

/* 4. Calculate available seats */
$ticketStmt = $pdo->prepare("SELECT COUNT(*) FROM Tickets WHERE event_id = ?");
$ticketStmt->execute([$event_id]);
$tickets_sold = $ticketStmt->fetchColumn();
$available_seats = $event['total_seats'] - $tickets_sold;

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 800px; margin: 2rem auto;">
    <h1 style="color: var(--mocha); font-size: 2.5rem;">
        <?= htmlspecialchars($event['title']) ?>
    </h1>

    <p class="text-muted">
        Organized by:
        <strong style="color: var(--paprika);">
            <?= htmlspecialchars($event['organizer_name']) ?>
        </strong>
    </p>

    <hr>

    <p><strong>About this event:</strong></p>
    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

    <div style="margin-top: 20px;">
        <p><strong>Date & Time:</strong>
            <?= date('F j, Y, g:i A', strtotime($event['date'])) ?>
        </p>
        <p><strong>Location:</strong>
            <?= htmlspecialchars($event['location']) ?>
        </p>
    </div>

    <div style="margin: 20px 0; font-size: 1.2rem;">
        <strong>Tickets Available:</strong>
        <span style="color: <?= $available_seats > 0 ? 'green' : 'red' ?>;">
            <?= $available_seats ?> / <?= $event['total_seats'] ?>
        </span>
    </div>

    <?php if ($available_seats > 0): ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="book_ticket.php" method="POST">
                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                <button type="submit" class="btn btn-primary">
                    Book Ticket
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                Please <a href="login.php">login</a> to book a ticket.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <button disabled class="btn btn-outline">
            Event Sold Out
        </button>
    <?php endif; ?>
</div>

</body>
</html>