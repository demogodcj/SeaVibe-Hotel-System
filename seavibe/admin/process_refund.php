<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usermail'])) {
    header("location: http://localhost/hotelmanage_system/index.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    header("Location: cancellations.php?msg=Invalid Booking ID");
    exit;
}

$booking_id = intval($_GET['booking_id']);

// Fetch booking info
$sql_check = "SELECT PaymentStatus, refunded FROM roombook WHERE id = $booking_id LIMIT 1";
$res = mysqli_query($conn, $sql_check);

if (!$res || mysqli_num_rows($res) == 0) {
    header("Location: cancellations.php?msg=Booking not found");
    exit;
}

$row = mysqli_fetch_assoc($res);

// Normalize payment status
$paymentStatus = strtolower(trim($row['PaymentStatus']));
$refunded = (int)$row['refunded'];

// Check payment + refund status
if ($paymentStatus !== 'paid') {
    header("Location: cancellations.php?msg=Booking not paid, cannot refund");
    exit;
}

if ($refunded === 1) {
    header("Location: cancellations.php?msg=Already refunded");
    exit;
}

// Process refund
$sql_update = "UPDATE roombook SET refunded = 1 WHERE id = $booking_id";
if (mysqli_query($conn, $sql_update)) {
    header("Location: cancellations.php?msg=Refund processed successfully");
    exit;
} else {
    header("Location: cancellations.php?msg=Error processing refund");
    exit;
}
?>
