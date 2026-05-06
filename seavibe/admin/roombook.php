<?php
session_start();

/* ✅ FIXED PATH */
include __DIR__ . '/../config.php';

if (!$conn) {
    die("Database connection failed");
}

/* ✅ Confirm booking */
if (isset($_GET['confirm_id'])) {
    $id = intval($_GET['confirm_id']);
    mysqli_query($conn, "UPDATE roombook SET stat='Confirmed' WHERE id=$id");
    header("Location: roombook.php");
    exit();
}

/* Fetch bookings */
$roombookresult = mysqli_query($conn, "SELECT * FROM roombook ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Room Booking</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f8f9fa;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.container-fluid {
    margin-top: 20px;
    padding-bottom: 40px;
}

h2 {
    margin-bottom: 20px;
    font-weight: 600;
    color: #333;
}

/* TABLE STYLE (same as payment page) */
table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    width: 100%;
}

th {
    background: #0d6efd; /* SAME BLUE */
    color: white;
    text-align: center;
    vertical-align: middle;
}

td {
    text-align: center;
    vertical-align: middle;
}

.btn {
    border-radius: 8px;
    white-space: nowrap;
    margin: 2px;
}

td .d-flex {
    flex-wrap: nowrap;
}

td .btn {
    margin: 2px;
    white-space: nowrap;
}
</style>

</head>

<body>

<div class="container-fluid">

    <!-- ✅ CENTERED TITLE -->
    <h2 class="text-center">🏨 Room Booking</h2>

    <?php if ($roombookresult && mysqli_num_rows($roombookresult) > 0): ?>

    <table class="table table-bordered table-hover align-middle">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Country</th>
                <th>Phone</th>
                <th>Room Type</th>
                <th>Bed</th>
                <th>No. Rooms</th>
                <th>Meal</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Days</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Action</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($res = mysqli_fetch_assoc($roombookresult)): ?>

            <?php
            $status = strtolower(trim($res['stat']));

            if (in_array($status, ['confirm', 'confirmed'])) {
                $badgeClass = 'success';
                $label = 'Confirmed';
                $payment = 'Paid';
            } else {
                $badgeClass = 'secondary';
                $label = 'Pending';
                $payment = '-';
            }
            ?>

            <tr>
                <td><?php echo $res['id']; ?></td>
                <td><?php echo htmlspecialchars($res['Name']); ?></td>
                <td><?php echo htmlspecialchars($res['Email']); ?></td>
                <td><?php echo htmlspecialchars($res['Country']); ?></td>
                <td><?php echo $res['Phone']; ?></td>
                <td><?php echo htmlspecialchars($res['RoomType']); ?></td>
                <td><?php echo htmlspecialchars($res['Bed']); ?></td>
                <td><?php echo $res['NoofRoom']; ?></td>
                <td><?php echo htmlspecialchars($res['Meal']); ?></td>
                <td><?php echo $res['cin']; ?></td>
                <td><?php echo $res['cout']; ?></td>
                <td><?php echo $res['nodays']; ?></td>

                <!-- STATUS -->
                <td>
                    <span class="badge bg-<?php echo $badgeClass; ?>">
                        <?php echo $label; ?>
                    </span>
                </td>

                <!-- PAYMENT -->
                <td>
                    <?php echo ($payment === 'Paid') 
                        ? "<span class='badge bg-success'>Paid</span>" 
                        : "-"; ?>
                </td>

                <!-- ACTION -->
                <!-- ACTIONS COLUMN -->
                <td>
                    <div class="d-flex justify-content-center gap-2 flex-nowrap">

                        <?php if ($status !== 'confirmed'): ?>
                            <a href="roombook.php?confirm_id=<?php echo $res['id']; ?>" 
                            class="btn btn-success btn-sm">
                            Confirm
                            </a>

                            <a href="roombookdelete.php?id=<?php echo $res['id']; ?>" 
                            class="btn btn-warning btn-sm"
                            onclick="return confirm('Cancel this booking?');">
                            Cancel
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>

                    </div>
                </td>

                <!-- EDIT COLUMN -->
                <td>
                    <a href="roombookedit.php?id=<?php echo $res['id']; ?>" 
                    class="btn btn-primary btn-sm">
                    Edit
                    </a>
                </td>

                <!-- DELETE COLUMN -->
                <td>
                    <a href="roombookdelete.php?id=<?php echo $res['id']; ?>" 
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Delete this record permanently?');">
                    Delete
                    </a>
                </td>
            </tr>

        <?php endwhile; ?>
        </tbody>

    </table>

    <?php else: ?>
        <div class="alert alert-warning text-center">No bookings found.</div>
    <?php endif; ?>

</div>

</body>
</html>