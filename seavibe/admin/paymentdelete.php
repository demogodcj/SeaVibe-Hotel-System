<?php
include('../config.php');
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize input

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM roombook WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: payment.php?msg=Payment deleted successfully");
        exit;
    } else {
        $stmt->close();
        header("Location: payment.php?msg=Error deleting payment");
        exit;
    }
} else {
    header("Location: payment.php?msg=Invalid request");
    exit;
}
