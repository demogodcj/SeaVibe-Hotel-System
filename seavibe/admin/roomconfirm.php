<?php
session_start();
include '../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Update booking status to Confirm
    $update_sql = "UPDATE roombook 
                   SET stat = 'Confirm', payment_status = 'Paid' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        // Redirect back with success message
        header("Location: roombook.php?success=1");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
} else {
    header("Location: roombook.php");
    exit();
}
?>
