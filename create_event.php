<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<div style='padding: 20px; color: red;'>Access Denied.</div>");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $raw_date = $_POST['date'];
    $location = trim($_POST['location']);
    $total_seats = (int)$_POST['total_seats'];
    $organizer_id = $_SESSION['user_id'];

    if (empty($title) || empty($raw_date) || empty($location) || $total_seats <= 0) {
        $error = 'Please fill in all required fields correctly.';
    } else {
        try {
            $formatted_date = date('Y-m-d H:i:s', strtotime($raw_date));
            $stmt = $pdo->prepare("INSERT INTO Events (organizer_id, title, description, date, location, total_seats) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$organizer_id, $title, $description, $formatted_date, $location, $total_seats])) {
                $success = 'Event created successfully! <a href="dashboard.php" style="color: var(--mocha); font-weight: bold;">Go to Dashboard</a>.';
            } else {
                $error = 'Failed to create event.';
            }
        } catch (PDOException $e) {
            $error = 'Database Error: ' . $e->getMessage();
        }
    }
}
require_once 'includes/header.php'; 
?>
<div class="card card-center" style="max-width: 650px; margin-top: 2rem;">
    <h2 style="margin-bottom: 25px; border-bottom: 2px solid var(--smoke); padding-bottom: 10px; color: var(--mocha);">Launch a New Event</h2>
    <?php if ($error): ?> <div class="alert alert-error"><?= $error ?></div> <?php endif; ?>
    <?php if ($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php else: ?>
        <form method="POST" action="create_event.php">
            <div class="form-group">
                <label for="title">Event Title <span style="color: var(--paprika);">*</span></label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description">Event Description</label>
                <textarea name="description" id="description" rows="4"></textarea>
            </div>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="date">Date & Time <span style="color: var(--paprika);">*</span></label>
                    <input type="datetime-local" name="date" id="date" required>
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="total_seats">Total Seats <span style="color: var(--paprika);">*</span></label>
                    <input type="number" name="total_seats" id="total_seats" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label for="location">Location <span style="color: var(--paprika);">*</span></label>
                <input type="text" name="location" id="location" required>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Publish Event</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>