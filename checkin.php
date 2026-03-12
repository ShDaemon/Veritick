<?php
require_once 'includes/db.php';

/* Safe session start */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Admin-only */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<div style='padding:20px;color:red;'>Access Denied</div>");
}

$scanner_id = $_SESSION['user_id'];
$message = '';
$message_color = '';
$ticket_data = null;

/* Handle scan or manual input */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['qr_payload'])) {

    $payload = trim($_POST['qr_payload']);

    $stmt = $pdo->prepare("
        SELECT 
            t.ticket_id,
            t.used,
            t.used_at,
            e.title AS event_title,
            e.organizer_id,
            u.name AS holder_name,
            u.email AS holder_email
        FROM Tickets t
        JOIN Events e ON t.event_id = e.event_id
        JOIN Users u ON t.user_id = u.user_id
        WHERE t.qr_code = ?
        LIMIT 1
    ");
    $stmt->execute([$payload]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        $message = "❌ Invalid Ticket";
        $message_color = "var(--paprika)";

    } elseif ($ticket['organizer_id'] != $scanner_id) {
        $message = "❌ Unauthorized Ticket";
        $message_color = "var(--paprika)";

    } elseif ($ticket['used'] == 1) {
        $message = "⚠️ Already Scanned on " . date('M j, Y g:i A', strtotime($ticket['used_at']));
        $message_color = "var(--mocha)";
        $ticket_data = $ticket;

    } else {
        $update = $pdo->prepare("
            UPDATE Tickets
            SET used = 1,
                used_at = NOW(),
                used_by_scanner_id = ?
            WHERE ticket_id = ?
        ");
        $update->execute([$scanner_id, $ticket['ticket_id']]);

        $message = "✅ ENTRY GRANTED";
        $message_color = "var(--green)";
        $ticket_data = $ticket;
    }
}

require_once 'includes/header.php';
?>

<script src="https://unpkg.com/html5-qrcode"></script>

<div class="card card-center" style="max-width:650px;text-align:center;">

    <h2>🎫 Event Check-In Scanner</h2>

    <?php if ($message): ?>
        <div style="margin:20px 0;padding:15px;
            border:2px solid <?= $message_color ?>;
            color:<?= $message_color ?>;
            font-weight:700;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Ticket Holder Details -->
    <?php if ($ticket_data): ?>
        <div style="text-align:left;padding:15px;border:1px solid #ddd;margin-bottom:20px;">
            <p><strong>Ticket ID:</strong> #<?= str_pad($ticket_data['ticket_id'], 6, '0', STR_PAD_LEFT) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($ticket_data['holder_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($ticket_data['holder_email']) ?></p>
            <p><strong>Event:</strong> <?= htmlspecialchars($ticket_data['event_title']) ?></p>
        </div>
    <?php endif; ?>

    <!-- Camera Scanner -->
    <div id="reader" style="width:100%;margin-bottom:20px;"></div>

    <!-- Manual Input -->
    <form method="POST">
        <input type="text"
               name="qr_payload"
               placeholder="Enter QR / Ticket Code manually"
               required
               style="width:100%;padding:12px;font-size:1.1rem;text-align:center;">
        <button class="btn btn-primary" style="margin-top:15px;">
            Verify Ticket
        </button>
    </form>

</div>

<script>
const scanner = new Html5Qrcode("reader");

scanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    qrText => {
        document.querySelector('[name="qr_payload"]').value = qrText;
        scanner.stop();
        document.forms[0].submit();
    }
);
</script>

</body>
</html>