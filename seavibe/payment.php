<?php
include('config.php');
session_start();

if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])) {
    $bookingId = $_GET['id'];

    // Fetch booking
    $sql = "SELECT * FROM roombook WHERE id='$bookingId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if($row) {
        $roomPrices = [
            "Superior Room" => 900,
            "Deluxe Room"   => 950,
            "Guest House"   => 800,
            "Single Room"   => 500
        ];

        $days = max(1, ceil((strtotime($row['cout']) - strtotime($row['cin'])) / (60*60*24)));
        $noOfRooms = (int)$row['NoofRoom'];
        $totalPrice = ($roomPrices[$row['RoomType']] ?? 0) * $noOfRooms * $days;

        // Update booking as Paid with total price
        $update = "UPDATE roombook SET PaymentStatus='Paid', TotalPrice='$totalPrice' WHERE id='$bookingId'";
        mysqli_query($conn, $update);

        // Redirect back to mybookings
        header("Location: mybookings.php");
        exit();
    }
}
?>
