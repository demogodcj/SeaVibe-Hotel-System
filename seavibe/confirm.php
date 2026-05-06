<?php
include('config.php');
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "UPDATE roombook SET stat='Confirm' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Always redirect back to bookings page
header("Location: mybookings.php");
exit();
?>
