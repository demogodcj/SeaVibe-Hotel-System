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

// --------------------
// Fetch all bookings
// --------------------
$sql = "SELECT id, RoomType, Bed, Meal, cin, cout, NoofRoom, stat 
        FROM roombook";
$result = $conn->query($sql);

$total_profit = 0;
$all_count = 0;
$statuses = [];
$rooms = [];
$beds = [];
$meals = [];
$revenues = array_fill(1, 12, 0);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_count++;

        // Normalize status
        $stat = trim($row['stat']);
        if (in_array($stat, ['Confirm', 'Confirmed'])) $stat = 'Confirmed';
        elseif (in_array($stat, ['Not Confirm', 'NotConfirm', 'Pending'])) $stat = 'Pending';
        elseif (in_array($stat, ['Cancel', 'Cancelled'])) $stat = 'Cancelled';

        $statuses[$stat] = ($statuses[$stat] ?? 0) + 1;

        // Count breakdowns
        $rooms[$row['RoomType']] = ($rooms[$row['RoomType']] ?? 0) + 1;
        $beds[$row['Bed']] = ($beds[$row['Bed']] ?? 0) + 1;
        $meals[$row['Meal']] = ($meals[$row['Meal']] ?? 0) + 1;

        // Calculate stay duration
        $days = max(1, ceil((strtotime($row['cout']) - strtotime($row['cin'])) / (60*60*24)));

        // Calculate dynamic price
        $roomCost = $roomPrices[$row['RoomType']] ?? 0;
        $bedCost  = $bedPrices[$row['Bed']] ?? 0;
        $mealCost = $mealPrices[$row['Meal']] ?? 0;

        $pricePerDay = $roomCost + $bedCost + $mealCost;
        $totalPrice = $pricePerDay * (int)$row['NoofRoom'] * $days;

        // Profit only from confirmed and not refunded
if ($stat === 'Confirmed' && ($row['refunded'] ?? 0) == 0) {
    $total_profit += $totalPrice;

    // Revenue by month
    $month = (int)date("n", strtotime($row['cin']));
    $revenues[$month] += $totalPrice;
}
    }
}

$monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
$monthLabels = json_encode(array_map(fn($m) => $monthNames[$m-1], range(1,12)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    ul { max-height:150px; overflow-y:auto; padding-left: 20px; }
    .card-body h5 { margin-bottom: 1rem; }
    .chart-container { position: relative; height: 320px; width: 100%; padding: 20px; }
  </style>
</head>
<body class="bg-light">
<div class="container my-4">
  <h2 class="text-center mb-4">📊 Hotel Dashboard</h2>

  <!-- Summary Cards -->
  <div class="row text-center mb-4">
    <div class="col-md-3">
      <div class="card shadow">
        <div class="card-body text-primary">
          <h5>Total Profit</h5>
          <h3>₹<?php echo number_format($total_profit,2); ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow">
        <div class="card-body text-dark">
          <h5>Total Bookings</h5>
          <h3><?php echo $all_count; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow">
        <div class="card-body text-success">
          <h5>Confirmed</h5>
          <h3><?php echo $statuses['Confirmed'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow">
        <div class="card-body text-warning">
          <h5>Pending</h5>
          <h3><?php echo $statuses['Pending'] ?? 0; ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Room, Bed, Meal Stats -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h6>Rooms by Type</h6>
          <ul>
            <?php foreach($rooms as $type=>$count) echo "<li>$type: $count</li>"; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h6>Beds</h6>
          <ul>
            <?php foreach($beds as $type=>$count) echo "<li>$type: $count</li>"; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h6>Meals</h6>
          <ul>
            <?php foreach($meals as $type=>$count) echo "<li>$type: $count</li>"; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue Line Chart -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <h5 class="mb-3">Revenue Growth</h5>
      <div class="chart-container">
        <canvas id="revenueChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Revenue Bar Chart -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <h5 class="mb-3">Monthly Revenue Comparison</h5>
      <div class="chart-container">
        <canvas id="revenueBarChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
const monthLabels = <?php echo $monthLabels; ?>;
const revenueData = <?php echo json_encode(array_values($revenues)); ?>;

// Line Chart
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueData,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 3,
            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
            pointRadius: 6,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, labels: { font: { size: 14, weight: 'bold' } } },
            tooltip: { callbacks: { label: ctx => '₹ ' + Number(ctx.raw).toLocaleString() } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: value => '₹' + value } }
        }
    }
});

// Bar Chart
new Chart(document.getElementById('revenueBarChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueData,
            backgroundColor: 'rgba(255, 159, 64, 0.7)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1,
            barPercentage: 0.6,
            categoryPercentage: 0.6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true },
            tooltip: { callbacks: { label: ctx => '₹ ' + Number(ctx.raw).toLocaleString() } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: value => '₹' + value } }
        }
    }
});
</script>
</body>
</html>
