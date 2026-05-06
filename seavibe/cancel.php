<?php
include('config.php');
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch booking before cancelling
    $sql = "SELECT * FROM roombook WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        // Insert into cancelled_bookings table
        $sqlInsert = "INSERT INTO cancelled_bookings 
            (booking_id, name, email, room_type, check_in, check_out, cancel_reason, cancelled_by, cancel_date) 
            VALUES (?, ?, ?, ?, ?, ?, 'User request', ?, NOW())";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param(
            "issssss",
            $booking['id'],
            $booking['Name'],
            $booking['Email'],
            $booking['RoomType'],
            $booking['cin'],
            $booking['cout'],
            $_SESSION['usermail'] // cancelled_by
        );
        $stmt->execute();
        $stmt->close();

        // Update booking as cancelled
        $sqlUpdate = "UPDATE roombook SET stat='Cancelled' WHERE id=?";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Always redirect back to bookings page
header("Location: mybookings.php");
exit();
?>
