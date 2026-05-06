<?php
include('../config.php');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch booking
    $query = "SELECT * FROM roombook WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    if($booking) {
        // Save to cancelled_bookings
        $reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? 'No reason provided');
        $cancelled_by = 'admin'; // Change to logged-in user if needed

        $insert = "INSERT INTO cancelled_bookings 
        (booking_id, name, email, phone, room_type, check_in, check_out, cancelled_by, cancel_reason)
        VALUES (
            '{$booking['id']}', '{$booking['name']}', '{$booking['email']}', '{$booking['phone']}',
            '{$booking['room_type']}', '{$booking['cin']}', '{$booking['cout']}',
            '$cancelled_by', '$reason'
        )";
        mysqli_query($conn, $insert);

        // Delete from active bookings
        mysqli_query($conn, "DELETE FROM roombook WHERE id='$id'");

        // Mark room available
        mysqli_query($conn, "UPDATE room SET place='Free' WHERE type='{$booking['room_type']}'");

        header("Location: cancelledbookings.php?msg=Booking cancelled successfully");
        exit();
    }
}
?>
