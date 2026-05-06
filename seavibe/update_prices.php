can you update mybookings.php<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("config.php"); // database connection

// ✅ Check if user is logged in
if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['usermail'];

// ✅ Cancel booking
if (isset($_POST['cancel_id'])) {
    $booking_id = intval($_POST['cancel_id']);
    $reason     = mysqli_real_escape_string($conn, $_POST['reason']);

    $insert = "INSERT INTO cancellations (roombook_id, reason, refund_status, canceled_by) 
               VALUES ('$booking_id', '$reason', 'Pending', '$email')";
    mysqli_query($conn, $insert);

    $update = "UPDATE roombook SET stat='Cancelled' WHERE id='$booking_id' AND Email='$email'";
    mysqli_query($conn, $update);

    echo "<script>alert('Booking cancelled successfully!');</script>";
}

// ✅ Confirm booking
if (isset($_POST['confirm_id'])) {
    $booking_id = intval($_POST['confirm_id']);

    $update = "UPDATE roombook SET stat='Confirmed' WHERE id='$booking_id' AND Email='$email'";
    mysqli_query($conn, $update);

    echo "<script>alert('Booking confirmed successfully!');</script>";
}

// ✅ Fetch bookings
$sql  = "SELECT * FROM roombook WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>My Bookings</h2>

<?php if ($result->num_rows > 0) { ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Room Type</th>
                <th>Bedding</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['RoomType']; ?></td>
                    <td><?php echo $row['Bed']; ?></td>
                    <td><?php echo $row['cin']; ?></td>
                    <td><?php echo $row['cout']; ?></td>
                    <td><?php echo $row['stat']; ?></td>
                    <td>
                        <?php if ($row['stat'] === 'NotConfirm') { ?>
                            <!-- Confirm Button -->
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="confirm_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                            </form>

                            <!-- Cancel Button -->
                            <form method="POST" style="display:inline-block;" 
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                <input type="hidden" name="cancel_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="reason" value="User requested cancellation">
                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                            </form>
                        <?php } elseif ($row['stat'] === 'Cancelled') { ?>
                            <span class="text-muted">Already Cancelled</span>
                        <?php } elseif ($row['stat'] === 'Confirmed') { ?>
                            <span class="text-success fw-bold">Confirmed ✅</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <p>No bookings found.</p>
<?php } ?>

</body>
</html>
