<?php
include('config.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Update booking to Paid
    $sql = "UPDATE roombook 
            SET payment_status='Paid', payment_date=NOW() 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: mybookings.php?msg=paid");
        exit();
    } else {
        echo "Error updating payment: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
