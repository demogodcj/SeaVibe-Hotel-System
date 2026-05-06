<?php
include('../config.php');
$result = mysqli_query($conn, "SELECT * FROM cancelled_bookings ORDER BY cancel_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cancelled Bookings</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<h2>Cancelled Bookings</h2>
<?php if(isset($_GET['msg'])): ?>
<p style="color:green;"><?= $_GET['msg'] ?></p>
<?php endif; ?>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Room Type</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Cancelled By</th>
        <th>Reason</th>
        <th>Date</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['booking_id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['room_type'] ?></td>
        <td><?= $row['check_in'] ?></td>
        <td><?= $row['check_out'] ?></td>
        <td><?= $row['cancelled_by'] ?></td>
        <td><?= $row['cancel_reason'] ?></td>
        <td><?= $row['cancel_date'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
