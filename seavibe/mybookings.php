
<?php
session_start();
include('config.php');


// Redirect if not logged in
if (!isset($_SESSION['usermail'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['usermail'] ?? '';

// Prices
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

function normalize_stat($raw) {
    $raw = strtolower(trim($raw));
    if ($raw === 'notconfirm') return 'pending';
    if ($raw === 'confirm' || $raw === 'confirmed') return 'confirmed';
    if ($raw === 'cancel' || $raw === 'cancelled') return 'cancelled';
    return 'pending';
}

// Handle actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    $rowRes = $conn->query("SELECT * FROM roombook WHERE id=$id AND Email='" . $conn->real_escape_string($email) . "' LIMIT 1");
    $row = $rowRes ? $rowRes->fetch_assoc() : null;

    if ($row) {
        $msg = '';
        if ($action === 'confirm') {
            $conn->query("UPDATE roombook SET stat='Confirmed' WHERE id=$id AND Email='" . $conn->real_escape_string($email) . "'");
            $msg = "Booking confirmed successfully!";
        } elseif ($action === 'cancel') {
            $conn->query("UPDATE roombook SET stat='Cancelled' WHERE id=$id AND Email='" . $conn->real_escape_string($email) . "'");
            $msg = "Booking cancelled successfully!";
        } elseif ($action === 'pay') {
            $nodays = (int)$row['nodays'];
            if ($nodays <= 0) {
                $cin = strtotime($row['cin']);
                $cout = strtotime($row['cout']);
                $nodays = max(1, ceil(($cout - $cin) / (60*60*24)));
            }
            $totalPrice = calculateTotalPrice($row['RoomType'], $row['Bed'], $row['Meal'], $nodays, $roomPrices, $bedPrices, $mealPrices);
            $conn->query("UPDATE roombook 
                          SET PaymentStatus='Paid', TotalPrice='" . floatval($totalPrice) . "' 
                          WHERE id=$id AND Email='" . $conn->real_escape_string($email) . "'");
            $msg = "Payment completed successfully!";
        }

        // Redirect with message
        header("Location: mybookings.php?msg=" . urlencode($msg));
        exit();
    }
}

// Fetch bookings
$sql = "SELECT id, RoomType, Bed, cin, cout, nodays, Meal, stat, TotalPrice, PaymentStatus 
        FROM roombook 
        WHERE Email='" . $conn->real_escape_string($email) . "' 
        ORDER BY id DESC";
$result = $conn->query($sql);

$total = $active = $cancelled = 0;
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

        $statNorm = normalize_stat($row['stat']);
        if ($statNorm === 'cancelled') {
            $cancelled++;
        } else {
            $active++;
        }
        $total++;
        $row['statNorm'] = $statNorm;
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Bookings</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{
            --blue-strong: #0077d9;
            --blue-soft: #cfe9ff;
            --bg: #f5fbff;
            --card: #ffffff;
            --border: #d6eaff;
        }
        body{
            font-family: Inter, Arial, sans-serif;
            background: var(--bg);
            margin: 20px;
            color: #1b1b1b;
        }
        h2{ color: var(--blue-strong); text-align: center; margin-bottom: 6px; }
        .summary{ text-align:center; color:#345; }
        .card{
            background: var(--card);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 6px 18px rgba(10,40,80,0.06);
            border: 1px solid var(--border);
        }
        table{
            width:100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        thead th{
            background: #bfe6ff;
            color: #033a6b;
            font-weight: 600;
            padding:10px 12px;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
        }
        tbody td{
            padding:10px 12px;
            border-right: 1px solid var(--border);
            border-top: 1px solid var(--border);
            background: #fff;
            word-wrap: break-word;
        }
        tbody td:last-child{ border-right: none; }
        tbody tr:nth-child(even) td{ background: #f0f9ff; }
        tbody tr:hover td{ background: #e6f6ff; }
        .btn{
            display:inline-block;
            padding:6px 12px;
            border-radius:8px;
            text-decoration:none;
            color:#fff;
            font-weight:600;
            font-size:13px;
        }
        .btn-pay{ background: var(--blue-strong); }
        .btn-confirm{ background: #28a745; }
        .btn-cancel{ background: #dc3545; }
        .btn-cancel {
         margin-top: 5px;
        }
        .status-confirm{ color:#1b7a2a; font-weight:700; }
        .status-cancel{ color:#b02a37; font-weight:700; }
        .status-pend{ color:#b06b00; font-weight:700; }
        .alert-success{
            padding:10px; 
            margin-bottom:15px; 
            border:1px solid #c3e6cb; 
            background:#d4edda; 
            color:#155724; 
            border-radius:5px;
            text-align:center;
        }
    </style>
</head>
<body>

<h2>My Bookings</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert-success">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<!-- Summary + Top Right Button -->
<div style="display:flex; justify-content: space-between; align-items: center; margin-bottom:10px;">
    <p class="summary">Total: <?= $total ?> &nbsp; | &nbsp; Active: <?= $active ?> &nbsp; | &nbsp; Cancelled: <?= $cancelled ?></p>
    <a href="cancel_paid.php" class="btn btn-cancel" style="padding:6px 12px; border-radius:8px;">Cancel Paid/Confirmed Booking</a>
</div>

<div class="card">
<?php if ($total > 0): ?>
    <table>
        <thead>
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
                <th>Pay</th>
                <th>Other Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($bookings as $row): 
            $isConfirmed = $row['statNorm'] === 'confirmed';
            $isCancelled = $row['statNorm'] === 'cancelled';
            $isPending   = $row['statNorm'] === 'pending';

            $isPaid = strtolower(trim($row['PaymentStatus'])) === 'paid';
            $displayPrice = number_format($row['TotalPrice'], 2);
        ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['RoomType']) ?></td>
                <td><?= htmlspecialchars($row['Bed']) ?></td>
                <td><?= htmlspecialchars($row['cin']) ?></td>
                <td><?= htmlspecialchars($row['cout']) ?></td>
                <td><?= htmlspecialchars($row['nodays']) ?></td>
                <td><?= htmlspecialchars($row['Meal']) ?></td>
                <td>
                    <?php if ($isConfirmed): ?>
                        <span class="status-confirm">Confirmed ✅</span>
                    <?php elseif ($isCancelled): ?>
                        <span class="status-cancel">Cancelled ❌</span>
                    <?php else: ?>
                        <span class="status-pend">Pending ⏳</span>
                    <?php endif; ?>
                </td>
                <td>₹<?= $displayPrice ?></td>
                <td>
                    <?php if ($isPaid): ?>
                        <span class="status-confirm">Paid ✅</span>
                    <?php else: ?>
                        <span class="status-pend">Pending</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$isPaid && !$isCancelled): ?>
                        <a class="btn btn-pay" href="mybookings.php?action=pay&id=<?= intval($row['id']) ?>">Pay</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($isPending): ?>
                        <a class="btn btn-confirm" href="mybookings.php?action=confirm&id=<?= intval($row['id']) ?>">Confirm</a>
                        &nbsp;
                        <a class="btn btn-cancel" href="mybookings.php?action=cancel&id=<?= intval($row['id']) ?>">Cancel</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">No bookings found.</p>
<?php endif; ?>
</div>

</body>
</html>
