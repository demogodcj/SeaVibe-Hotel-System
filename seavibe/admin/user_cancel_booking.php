<?php
include 'config.php';

if (isset($_GET['id']) && isset($_GET['email'])) {
    $id = intval($_GET['id']);
    $email = $_GET['email'];

    // Verify booking belongs to this email
    $stmt = $conn->prepare("SELECT * FROM roombook WHERE id = ? AND Email = ?");
    $stmt->bind_param("is", $id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if ($booking) {
        // Update booking status
        $conn->query("UPDATE roombook SET stat = 'Canceled' WHERE id = $id");

        // Insert into cancellations table
        $reason = "User requested cancellation";
        $refund_status = "Pending";
        $stmt2 = $conn->prepare("INSERT INTO cancellations (roombook_id, reason, refund_status, canceled_by) VALUES (?, ?, ?, 'user')");
        $stmt2->bind_param("iss", $id, $reason, $refund_status);
        $stmt2->execute();

        header("Location: mybookings.php?success=1");
        exit;
    } else {
        echo "Booking not found or email does not match.";
    }
} else {
    echo "Invalid request.";
}
?>
