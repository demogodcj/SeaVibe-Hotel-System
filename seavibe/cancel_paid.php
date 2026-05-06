<?php
session_start();
include('config.php');

if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['usermail'] ?? '';

$roomPrices = [
    "Superior Room" => 900,
    "Deluxe Room"   => 950,
    "Guest House"   => 800,
    "Single Room"   => 700
];

$bedPrices = [
    "Single" => 0,
    "Double" => 200,
    "Triple" => 400,
    "Quad"   => 600
];

$mealPrices = [
    "Room only"  => 0,
    "Breakfast"  => 100,
    "Half Board" => 200,
    "Full Board" => 300
];

function calculateTotalPrice($roomType, $bed, $meal, $nodays, $roomPrices, $bedPrices, $mealPrices) {
    $roomCost = $roomPrices[$roomType] ?? 0;
    $bedCost  = $bedPrices[$bed] ?? 0;
    $mealCost = $mealPrices[$meal] ?? 0;
    return ($roomCost + $bedCost + $mealCost) * max(1, (int)$nodays);
}

// Handle cancel request
$showToast = false;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'cancel') {
    $id = intval($_GET['id']);
    $conn->query("UPDATE roombook SET stat='Cancelled' WHERE id=$id AND Email='" . $conn->real_escape_string($email) . "'");
    $showToast = true;
}

// Fetch all confirmed but not cancelled bookings
$sql = "SELECT id, RoomType, Bed, cin, cout, nodays, Meal, stat, TotalPrice, PaymentStatus
        FROM roombook
        WHERE Email='" . $conn->real_escape_string($email) . "'
          AND LOWER(TRIM(stat)) IN ('confirm','confirmed')
          AND LOWER(TRIM(stat)) <> 'cancelled'
        ORDER BY id DESC";

$result = $conn->query($sql);
$bookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nodays = (int)$row['nodays'];
        if ($nodays <= 0) {
            $cin_ts = strtotime($row['cin']);
            $cout_ts = strtotime($row['cout']);
            $nodays = max(1, ceil(($cout_ts - $cin_ts) / (60*60*24)));
            $row['nodays'] = $nodays;
        }

        if ((float)$row['TotalPrice'] <= 0) {
            $row['TotalPrice'] = calculateTotalPrice($row['RoomType'], $row['Bed'], $row['Meal'], $row['nodays'], $roomPrices, $bedPrices, $mealPrices);
        }

        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancel Confirmed Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f5fbff; margin: 20px; }
        h2 { text-align: center; color: #0077d9; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d6eaff; padding: 10px; text-align: center; }
        th { background: #bfe6ff; color: #033a6b; }
        tr:nth-child(even) td { background: #f0f9ff; }
        tr:hover td { background: #e6f6ff; }
        .btn-cancel { background: #dc3545; color: #fff; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .status-confirm { color:#1b7a2a; font-weight:700; }
        .status-pend { color:#b06b00; font-weight:700; }
        .status-paid { color:#0077d9; font-weight:700; }
    </style>
</head>
<body>

<h2>Cancel Confirmed Bookings</h2>

<?php if (count($bookings) > 0): ?>
<table>
    <tr>
        <th>Booking ID</th>
        <th>Room Type</th>
        <th>Bedding</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Days</th>
        <th>Meal</th>
        <th>Status</th>
        <th>Total Price</th>
        <th>Payment</th>
        <th>Cancel</th>
    </tr>
    <?php foreach ($bookings as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['RoomType']) ?></td>
        <td><?= htmlspecialchars($row['Bed']) ?></td>
        <td><?= htmlspecialchars($row['cin']) ?></td>
        <td><?= htmlspecialchars($row['cout']) ?></td>
        <td><?= htmlspecialchars($row['nodays']) ?></td>
        <td><?= htmlspecialchars($row['Meal']) ?></td>
        <td class="status-confirm">Confirmed ✅</td>
        <td>₹<?= number_format($row['TotalPrice'], 2) ?></td>
        <td class="<?= strtolower(trim($row['PaymentStatus']))==='paid'?'status-paid':'status-pend' ?>">
            <?= ucfirst($row['PaymentStatus']) ?> <?= strtolower(trim($row['PaymentStatus']))==='paid'?'✅':'⏳' ?>
        </td>
        <td>
            <a class="btn-cancel" href="cancel_paid.php?action=cancel&id=<?= intval($row['id']) ?>">Cancel</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No confirmed bookings available.</p>
<?php endif; ?>

<!-- Bootstrap Toast -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
  <div id="cancelToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Booking cancelled successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
<?php if($showToast): ?>
var toast = new bootstrap.Toast(document.getElementById('cancelToast'));
toast.show();
<?php endif; ?>
</script>

</body>
</html>
