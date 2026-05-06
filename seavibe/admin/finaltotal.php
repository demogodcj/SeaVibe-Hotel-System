<?php
include '../config.php'; // connect to DB

// Fetch all bookings
$bookings = mysqli_query($conn, "SELECT * FROM roombook");

while ($booking = mysqli_fetch_assoc($bookings)) {
    $roombook_id = $booking['id'];
    $room_type = $booking['RoomType'];
    $checkin = $booking['checkin'];
    $checkout = $booking['checkout'];

    // Calculate number of nights
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $interval = $checkin_date->diff($checkout_date);
    $nights = $interval->days;

    // Get room price
    $room_query = mysqli_query($conn, "SELECT price FROM room WHERE RoomType='$room_type' LIMIT 1");
    $room_data = mysqli_fetch_assoc($room_query);
    $price_per_night = $room_data['price'];

    // Calculate final total
    $finaltotal = $price_per_night * $nights;

    // Update payment table
    mysqli_query($conn, "UPDATE payment SET finaltotal='$finaltotal' WHERE roombook_id='$roombook_id'");
}

echo "All final totals have been updated successfully!";
?>
