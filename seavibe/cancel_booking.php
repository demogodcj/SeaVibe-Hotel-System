<?php
session_start();
include('config.php');

// Redirect if not logged in
if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['usermail'];

// Room base prices
$roomPrices = [
    "Superior Room" => 900,
    "Deluxe Room"   => 950,
    "Guest House"   => 800,
    "Single Room"   => 500
];

// Fetch active bookings for this user
$sqlActive = "
SELECT id, RoomType, COALESCE(Bed,'Single') AS Bed, cin, cout, COALESCE(NoofRoom,1) AS NoofRoom,
       COALESCE(Meal,'Room only') AS Meal, stat, COALESCE(PaymentStatus,'Pending') AS PaymentStatus
FROM roombook
WHERE Email='$email'
ORDER BY id DESC
";
$resultActive = mysqli_query($conn, $sqlActive);

// Fetch cancelled bookings for this user
$sqlCancelled = "
SELECT booking_id AS id, room_type AS RoomType, '-' AS Bed, check_in AS cin, check_out AS cout,
       1 AS NoofRoom, '-' AS Meal, 'Cancelled' AS stat, 'Pending' AS PaymentStatus
FROM cancelled_bookings
WHERE email='$email'
ORDER BY cancel_date DESC
";
$resultCancelled = mysqli_query($conn, $sqlCancelled);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f0f4f8; }
.table thead { background-color: #007bff; color: #fff; }
.btn-confirm { background-color: #28a745; border: none; }
.btn-confirm:hover { background-color: #1e7e34; }
.btn-pay { background-color: #007bff; border: none; }
.btn-pay:hover { background-color: #0056b3; }
.btn-cancel { background-color: #dc3545; border: none; }
.btn-cancel:hover { background-color: #a71d2a; }
.status-paid { color: #007bff; font-weight: bold; }
.status-confirmed { color: #198754; font-weight: bold; }
.status-cancelled { color: #6c757d; font-weight: bold; font-style: italic; }
</style>
</head>
<body>
<div class="container mt-5">
<h2 class="mb-4 text-primary">My Bookings</h2>

<?php
function showBookings($result, $roomPrices) {
    if(mysqli_num_rows($result) == 0) return;

    while($row = mysqli_fetch_assoc($result)) {
        $days = max(1, ceil((strtotime($row['cout']) - strtotime($row['cin'])) / (60*60*24)));
        $price = ($roomPrices[$row['RoomType']] ?? 0) * $row['NoofRoom'] * $days;
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['RoomType'] ?></td>
            <td><?= $row['Bed'] ?></td>
            <td><?= $row['cin'] ?></td>
            <td><?= $row['cout'] ?></td>
            <td><?= $days ?></td>
            <td><?= $row['Meal'] ?></td>
            <td>
                <?php 
                if($row['stat'] == 'NotConfirm') echo "<span class='text-primary'>Not Confirmed</span>";
                elseif($row['stat'] == 'Confirm') echo "<span class='status-confirmed'>Confirmed ✅</span>";
                elseif($row['stat'] == 'Cancelled') echo "<span class='status-cancelled'>Cancelled ❌</span>";
                ?>
            </td>
            <td>₹<?= number_format($price,2) ?></td>
            <td>
                <?= ($row['PaymentStatus'] == 'Paid') ? "<span class='status-paid'>Paid ✅</span>" : "<span class='text-primary'>Pending</span>" ?>
            </td>
            <td>
                <?php if(($row['PaymentStatus'] ?? '') != 'Paid' && $row['stat'] != 'Cancelled'): ?>
                    <a href="payment.php?id=<?= $row['id'] ?>" class="btn btn-pay btn-sm">Pay</a>
                <?php else: echo '-'; endif; ?>
            </td>
            <td>
                <div class="d-flex gap-2">
                <?php if($row['stat'] == 'NotConfirm'): ?>
                    <a href="confirm.php?id=<?= $row['id'] ?>" class="btn btn-confirm btn-sm">Confirm</a>
                    <a href="cancel.php?id=<?= $row['id'] ?>" class="btn btn-cancel btn-sm">Cancel</a>
                <?php elseif($row['stat'] == 'Confirm'): ?>
                    <span class="status-confirmed align-self-center">Confirmed ✅</span>
                <?php elseif($row['stat'] == 'Cancelled'): ?>
                    <span class="status-cancelled align-self-center">Cancelled ❌</span>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
}
?>

<table class="table table-bordered table-striped align-middle">
<thead>
<tr>
<th>Booking ID</th>
<th>Room Type</th>
<th>Bedding</th>
<th>Check In</th>
<th>Check Out</th>
<th>No. of Days</th>
<th>Meal</th>
<th>Status</th>
<th>Total Price</th>
<th>Payment Status</th>
<th>Pay</th>
<th>Other Actions</th>
</tr>
</thead>
<tbody>
<?php 
showBookings($resultActive, $roomPrices);
showBookings($resultCancelled, $roomPrices);
?>
</tbody>
</table>

<?php if(mysqli_num_rows($resultActive) + mysqli_num_rows($resultCancelled) == 0): ?>
<p class="text-danger">No bookings found.</p>
<?php endif; ?>

</div>
</body>
</html>
