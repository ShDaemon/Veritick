<?php
session_start();
require_once 'includes/db.php';
define('QR_SECRET_KEY', 'veritick_super_secret_key_2026');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = (int)$_POST['event_id'];

$stmt = $pdo->prepare("SELECT title, total_seats FROM Events WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    showStyledError("We couldn't find that event in our system.");
}

$ticketStmt = $pdo->prepare("SELECT COUNT(*) FROM Tickets WHERE event_id = ?");
$ticketStmt->execute([$event_id]);
$tickets_sold = $ticketStmt->fetchColumn();

if ($tickets_sold >= $event['total_seats']) {
    showStyledError("Too late! This event just sold out.");
}

$qr_payload = "VT-" . $user_id . "-" . $event_id . "-" . time() . "-" . bin2hex(random_bytes(4));
$qr_signature = hash_hmac('sha256', $qr_payload, QR_SECRET_KEY);

try {
    $insertStmt = $pdo->prepare("INSERT INTO Tickets (user_id, event_id, qr_code, qr_signature) VALUES (?, ?, ?, ?)");
    $insertStmt->execute([$user_id, $event_id, $qr_payload, $qr_signature]);
    header("Location: my_tickets.php?status=success");
    exit;
} catch (PDOException $e) {
    showStyledError("A database error occurred: " . $e->getMessage());
}

function showStyledError($error_message) {
    require_once 'includes/header.php';
    echo '<div class="card card-center" style="text-align: center; margin-top: 4rem;">';
    echo '<h2 style="color: var(--paprika); margin-bottom: 20px;">Booking Failed</h2>';
    echo '<div class="alert alert-error" style="margin-bottom: 25px;">' . htmlspecialchars($error_message) . '</div>';
    echo '<a href="index.php" class="btn btn-outline" style="width: auto;">Browse Other Events</a>';
    echo '</div></body></html>';
    exit;
}
?>