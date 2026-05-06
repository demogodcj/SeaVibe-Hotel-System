<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SeaVibe - Staff</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ===== BASE ===== */
body{
    background: linear-gradient(180deg,#eef6ff,#ffffff);
    font-family: Arial, sans-serif;
    color:#0b2a4a;
}

/* ===== ADD STAFF BOX ===== */
.addstaffsection{
    max-width:520px;
    margin:40px auto 20px;
    background:#ffffff;
    padding:25px;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border:1px solid #e6f0ff;
}

.addstaffsection h2{
    text-align:center;
    margin-bottom:20px;
    color:#0d6efd;
    font-weight:600;
}

.form-control, .form-select{
    border-radius:10px;
    border:1px solid #cfe2ff;
}

.form-control:focus, .form-select:focus{
    box-shadow:none;
    border-color:#0d6efd;
}

/* KEEP BUTTON BLUE (IMPORTANT) */
.btn-primary{
    width:100%;
    background:#0d6efd;
    border:none;
    border-radius:10px;
    font-weight:600;
}

/* ===== STAFF CARDS ===== */
.container{
    padding:30px 20px 80px;
}

.staffbox{
    background:#ffffff;
    border-radius:18px;
    padding:25px 20px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    border:1px solid #eaf2ff;
    transition:0.25s;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    height:100%;
}

.staffbox:hover{
    transform:translateY(-6px);
    box-shadow:0 12px 30px rgba(0,0,0,0.12);
}

.staffbox i{
    font-size:3rem;
    color:#0d6efd;
    margin-bottom:10px;
}

.staffbox h4{
    margin:10px 0 5px;
    font-size:1.2rem;
    font-weight:600;
}

.staffbox p{
    color:#5a6b7d;
    margin-bottom:15px;
}

/* KEEP DELETE BUTTON RED ONLY */
.btn-danger{
    width:100%;
    border-radius:10px;
}

/* GRID GAP FIX */
.row{
    row-gap:25px;
}

</style>
</head>

<body>

<!-- ADD STAFF -->
<div class="addstaffsection">
    <h2>Add Staff</h2>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="staffname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Work</label>
            <select name="staffwork" class="form-select" required>
                <option disabled selected>Select Role</option>
                <option value="Manager">Manager</option>
                <option value="Cook">Cook</option>
                <option value="Helper">Helper</option>
                <option value="Cleaner">Cleaner</option>
                <option value="Waiter">Waiter</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" name="addstaff">Add Staff</button>

    </form>

    <?php
    if (isset($_POST['addstaff'])) {
        $staffname = $_POST['staffname'];
        $staffwork = $_POST['staffwork'];

        $sql = "INSERT INTO staff(name, work) VALUES ('$staffname', '$staffwork')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: staff.php");
        }
    }
    ?>

</div>

<!-- STAFF LIST -->
<div class="container">
    <div class="row">

        <?php
        $sql = "SELECT * FROM staff";
        $re = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_array($re)) {
        ?>
        
        <div class="col-md-4 d-flex">
            <div class="staffbox w-100">

                <div>
                    <i class="fa fa-user-group"></i>
                    <h4><?= htmlspecialchars($row['name']) ?></h4>
                    <p><?= htmlspecialchars($row['work']) ?></p>
                </div>

                <a href="staffdelete.php?id=<?= $row['id'] ?>">
                    <button class="btn btn-danger">Delete</button>
                </a>

            </div>
        </div>

        <?php } ?>

    </div>
</div>

</body>
</html>