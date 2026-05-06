<?php
session_start();

/* ✅ FIX 1: SAFE ABSOLUTE CONFIG PATH */
include $_SERVER['DOCUMENT_ROOT'] . '/seavibe/config.php';

/* ✅ FIX 2: CORRECT SESSION CHECK (ADMIN USES staffmail, NOT usermail) */
if (!isset($_SESSION['staffmail'])) {
    header("Location: /seavibe/index.php");
    exit;
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;

/* Pricing rules */
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

/* Fetch cancelled bookings */
$sql = "SELECT r.id as rid, r.Name, r.Email, r.RoomType, r.Bed, r.Meal, r.NoofRoom, r.cin, r.cout, r.nodays, r.stat, r.PaymentStatus, r.refunded
        FROM roombook r
        WHERE r.stat = 'Cancelled'
        ORDER BY r.id DESC";

$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cancelled Bookings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
}
table.table {
    border-collapse: separate !important;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
}
.btn {
    border-radius: 8px;
}
table.table td,
table.table th {
    border: 1px solid #dee2e6 !important;
}
</style>
</head>

<body class="p-3">

<div class="container-fluid">

    <h3 class="text-center mb-3">❌ Cancelled Bookings</h3>

    <div class="d-flex justify-content-end mb-2">
        <!-- ✅ FIX 3: SAFE RELATIVE PATH -->
        <a class="btn btn-outline-secondary btn-sm" href="/seavibe/admin/roombook.php">
            Back to Bookings
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success py-2">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Booking ID</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Price</th>
                    <th>Paid</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $i = 1;

            if ($res && mysqli_num_rows($res) > 0) {

                while ($row = mysqli_fetch_assoc($res)) {

                    $roomCost = $roomPrices[$row['RoomType']] ?? 0;
                    $bedCost  = $bedPrices[$row['Bed']] ?? 0;
                    $mealCost = $mealPrices[$row['Meal']] ?? 0;

                    $pricePerDay = $roomCost + $bedCost + $mealCost;
                    $totalPrice = $pricePerDay * intval($row['nodays']);
            ?>

                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['rid']); ?></td>

                    <td>
                        <?= htmlspecialchars($row['Name']); ?><br>
                        <small class="text-muted">
                            <?= htmlspecialchars($row['Email']); ?>
                        </small>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['RoomType'] . ' / ' . $row['Bed']); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['cin']); ?> → <?= htmlspecialchars($row['cout']); ?><br>
                        <small class="text-muted">
                            <?= intval($row['nodays']); ?> nights
                        </small>
                    </td>

                    <td>₹<?= number_format($totalPrice, 2); ?></td>

                    <td>
                        <?= (strtolower(trim($row['PaymentStatus'])) === 'paid') ? 'Yes ✅' : 'No ⏳'; ?>
                    </td>

                    <td>
                        <?php if (($row['refunded'] ?? 0) == 0 && strtolower(trim($row['PaymentStatus'])) === 'paid'): ?>
                            <a href="/seavibe/admin/process_refund.php?booking_id=<?= $row['rid']; ?>" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Are you sure you want to refund this booking?');">
                               Refund
                            </a>

                        <?php elseif (($row['refunded'] ?? 0) == 1): ?>
                            <button class="btn btn-sm btn-secondary" disabled>
                                Refunded
                            </button>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>

            <?php
                }

            } else {
                echo "<tr>
                        <td colspan='8' class='text-center text-muted py-4'>
                            No cancelled bookings found
                        </td>
                      </tr>";
            }
            ?>

            </tbody>
        </table>

    </div>
</div>

</body>
</html>