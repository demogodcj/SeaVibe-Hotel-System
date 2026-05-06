<?php
include('../config.php');
session_start();

// --------------------
// Pricing Rules
// --------------------
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
    "Room only"       => 0,
    "Breakfast"       => 100,
    "Half Board"      => 200,
    "Full Board"      => 300
];

// Fetch all bookings/payments
$sql = "SELECT * FROM roombook ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Records</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background: #f8f9fa; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
    .container-fluid { margin-top: 20px; padding-bottom: 40px; }
    h2 { margin-bottom: 20px; font-weight: 600; color: #333; }
    table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 100%; }
    th { background: #0d6efd; color: white; text-align: center; vertical-align: middle; }
    td { text-align: center; vertical-align: middle; }
    .btn { border-radius: 8px; white-space: nowrap; }
    th:last-child, td:last-child { width: 120px; }
  </style>
</head>
<body>
<div class="container-fluid">
  <h2 class="text-center">💳 Payment Records</h2>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Room Type</th>
          <th>Bed</th>
          <th>Check-In</th>
          <th>Check-Out</th>
          <th>No. of Rooms</th>
          <th>Status</th>
          <th>Meal</th>
          <th>💰 Price Paid</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()):
            // Calculate dynamic price
            $roomCost = $roomPrices[$row['RoomType']] ?? 0;
            $bedCost  = $bedPrices[$row['Bed']] ?? 0;
            $mealCost = $mealPrices[$row['Meal']] ?? 0;
            $pricePerDay = $roomCost + $bedCost + $mealCost;
            $totalPrice = $pricePerDay * (int)$row['nodays'];

            // Normalize status
            $status = strtolower(trim($row['stat']));
            if (in_array($status, ['confirm', 'confirmed', 'paid'])) {
                $badgeClass = 'success';
                $label = 'Paid';
            } elseif ($status === 'cancelled') {
                $badgeClass = 'danger';
                $label = 'Cancelled';
            } else {
                $badgeClass = 'secondary';
                $label = ucfirst($row['stat']);
            }
        ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['Name']); ?></td>
            <td><?php echo htmlspecialchars($row['RoomType']); ?></td>
            <td><?php echo htmlspecialchars($row['Bed']); ?></td>
            <td><?php echo $row['cin']; ?></td>
            <td><?php echo $row['cout']; ?></td>
            <td><?php echo $row['NoofRoom']; ?></td>
            <td>
              <span class="badge bg-<?php echo $badgeClass; ?>">
                <?php echo $label; ?>
              </span>
            </td>
            <td><?php echo htmlspecialchars($row['Meal']); ?></td>
            <td><strong>₹<?php echo number_format($totalPrice, 2); ?></strong></td>
            <td>
              <a href="paymentdelete.php?id=<?php echo $row['id']; ?>" 
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to delete this payment?');">
                Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning text-center">No payment records found.</div>
  <?php endif; ?>
</div>
</body>
</html>
