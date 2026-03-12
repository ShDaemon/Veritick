<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Fetch ONLY tickets for upcoming events */
$stmt = $pdo->prepare("
    SELECT t.*, e.title, e.date, e.location 
    FROM Tickets t
    JOIN Events e ON t.event_id = e.event_id
    WHERE t.user_id = ?
    AND e.date >= NOW()
    ORDER BY e.date ASC
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div style="max-width: 900px; margin: 2rem auto;">
    <h2 style="margin-bottom: 25px; border-bottom: 2px solid var(--smoke); padding-bottom: 10px; color: var(--mocha);">
        My Digital Wallet
    </h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">Ticket booked successfully!</div>
    <?php endif; ?>

    <div style="display: flex; flex-direction: column; gap: 25px;">
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-card">
                    <div class="ticket-details">
                        <h3 style="color: var(--mocha); font-size: 1.8rem;">
                            <?= htmlspecialchars($ticket['title']) ?>
                        </h3>

                        <p>
                            <strong style="color: var(--green);">📅 Date:</strong>
                            <?= date('F j, Y, g:i A', strtotime($ticket['date'])) ?>
                        </p>

                        <p>
                            <strong style="color: var(--green);">📍 Venue:</strong>
                            <?= htmlspecialchars($ticket['location']) ?>
                        </p>

                        <p>
                            <strong style="color: var(--green);">🎟️ Ticket ID:</strong>
                            #<?= str_pad($ticket['ticket_id'], 6, '0', STR_PAD_LEFT) ?>
                        </p>

                        <?php if ($ticket['used'] == 1): ?>
                            <span class="badge badge-danger">Scanned / Used</span>
                        <?php else: ?>
                            <span class="badge badge-success">Valid Entry</span>
                        <?php endif; ?>
                    </div>

                    <div class="ticket-qr">
                        <p style="font-size: 0.85rem; font-weight: 700;">SCAN AT GATE</p>

                        <div class="qrcode-container"
                             data-payload="<?= htmlspecialchars($ticket['qr_code']) ?>"
                             style="background: white; padding: 10px; border-radius: 8px;">
                        </div>

                        <?php if ($ticket['used'] == 1): ?>
                            <div style="position:absolute; inset:0; background:rgba(245,245,245,.9);
                                        display:flex; align-items:center; justify-content:center;
                                        color:var(--paprika); font-size:1.8rem; font-weight:800;">
                                VOID
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p class="text-muted" style="text-align:center;">
                    No active tickets. Book an event!
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.qrcode-container').forEach(el => {
        new QRCode(el, {
            text: el.dataset.payload,
            width: 150,
            height: 150
        });
    });
});
</script>

</body>
</html>