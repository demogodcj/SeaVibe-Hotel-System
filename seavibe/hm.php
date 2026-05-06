<?php
session_start();
include('config.php'); 

if (!isset($_SESSION['usermail'])) {
    header("location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SeaVibe Reservation</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* ===== SEA THEME ===== */
body{
    font-family: Arial, sans-serif;
    margin:0;
    background:#ffffff;
    color:#001f3f;
}

/* NAVBAR (MATCH HOMEPAGE) */
.navbar{
    background: linear-gradient(90deg,#0077b6,#00b4d8);
    padding:12px 20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.15);
}

.navbar-brand{
    color:white !important;
    font-weight:700;
}

/* HEADINGS */
h2,h4{
    color:#001f3f;
    font-weight:700;
}

/* FORM CONTAINER */
.container{
    max-width:1100px;
}

/* FORM CARD */
.form-wrapper{
    background: rgba(255,255,255,0.9);
    border-radius:18px;
    padding:30px;
    border:1px solid rgba(0,119,182,0.2);
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
}

/* INPUTS */
.form-control, select{
    border-radius:10px;
    border:1px solid #cce5ff;
}

.form-control:focus, select:focus{
    border-color:#00b4d8;
    box-shadow:0 0 0 3px rgba(0,180,216,0.15);
}

/* PRICE BOX */
.price-section{
    margin-top:20px;
    padding:18px;
    border-radius:14px;
    background: linear-gradient(135deg,#e0f7ff,#ffffff);
    border:1px solid #b3e5ff;
    text-align:center;
}

.price-section h5{
    margin:0;
    font-weight:700;
    color:#0077b6;
}

/* BUTTON */
.btn-primary{
    background: linear-gradient(90deg,#0077b6,#00b4d8);
    border:none;
    border-radius:10px;
    font-weight:600;
}

.btn-primary:hover{
    background: linear-gradient(90deg,#005f87,#0096c7);
}

/* FOOTER */
footer{
    margin-top:40px;
    padding:15px;
    text-align:center;
    color:#001f3f;
    background:#f0fbff;
}

/* SECTION SPACING (IMPORTANT FIX) */
.row > div{
    margin-bottom:10px;
}

</style>
</head>

<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand"><i class="bi bi-water"></i> SeaVibe Hotel</a>
  </div>
</nav>

<!-- CONTENT -->
<div class="container my-4">

  <h2 class="text-center mb-4">
    <i class="bi bi-calendar-check"></i> Room Reservation
  </h2>

  <div class="form-wrapper">

  <form method="post">
    <div class="row g-4">

      <!-- LEFT -->
      <div class="col-md-6">
        <h4><i class="bi bi-person"></i> Guest Info</h4>

        <input type="text" class="form-control mb-2" name="Name" placeholder="Full Name" required>
        <input type="email" class="form-control mb-2" name="Email" placeholder="Email" required>
        <input type="text" class="form-control mb-2" name="Phone" placeholder="Phone" required>
      </div>

      <!-- RIGHT -->
      <div class="col-md-6">
        <h4><i class="bi bi-house-door"></i> Booking Info</h4>

        <select class="form-control mb-2" name="RoomType" required>
          <option value="">Room Type</option>
          <option>Superior Room</option>
          <option>Deluxe Room</option>
          <option>Guest House</option>
          <option>Single Room</option>
        </select>

        <select class="form-control mb-2" name="Bed" required>
          <option value="">Bed Type</option>
          <option>Single</option>
          <option>Double</option>
          <option>Triple</option>
          <option>Quad</option>
        </select>

        <select class="form-control mb-2" name="Meal" required>
          <option value="">Meal</option>
          <option>Room only</option>
          <option>Breakfast</option>
          <option>Half Board</option>
          <option>Full Board</option>
        </select>

        <select class="form-control mb-2" name="NoofRoom">
          <option>1</option>
          <option>2</option>
          <option>3</option>
        </select>

        <input type="date" class="form-control mb-2" name="cin">
        <input type="date" class="form-control mb-2" name="cout">

        <!-- PRICE -->
        <div class="price-section">
          <h5><i class="bi bi-cash"></i> Total: <span id="totalPrice">₹0</span></h5>
        </div>

        <!-- BUTTON -->
        <button type="submit" name="submitReservation" class="btn btn-primary w-100 mt-3">
          Confirm Booking
        </button>

      </div>

    </div>
  </form>

  </div>
</div>

<!-- PHP (UNCHANGED LOGIC) -->
<?php
if (isset($_POST['submitReservation'])) {
    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Phone = $_POST['Phone'];
    $RoomType = $_POST['RoomType'];
    $Bed = $_POST['Bed'];
    $NoofRoom = $_POST['NoofRoom'];
    $Meal = $_POST['Meal'];
    $cin = $_POST['cin'];
    $cout = $_POST['cout'];
    $sta = "NotConfirm";
    $nodays = (strtotime($cout) - strtotime($cin)) / (60*60*24);

    $sql = "INSERT INTO roombook (Name,Email,Phone,RoomType,Bed,NoofRoom,Meal,cin,cout,stat,nodays) 
            VALUES ('$Name','$Email','$Phone','$RoomType','$Bed','$NoofRoom','$Meal','$cin','$cout','$sta','$nodays')";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('Reservation successful!'); window.location='hm.php';</script>";
    } else {
        echo "<script>alert('Error! Please try again.');</script>";
    }
}
?>

<footer>
  <i class="bi bi-water"></i> SeaVibe • Ocean Luxury Hotel Experience
</footer>

<!-- PRICE SCRIPT (UNCHANGED) -->
<script>
const roomPrices = { "Superior Room": 900, "Deluxe Room": 950, "Guest House": 800, "Single Room": 500 };
const mealPrices = { "Room only": 0, "Breakfast": 100, "Half Board": 200, "Full Board": 300 };
const bedPrices = { "Single": 0, "Double": 100, "Triple": 200, "Quad": 300 };

function calculatePrice() {
    const roomType = document.querySelector("select[name='RoomType']").value;
    const bedType = document.querySelector("select[name='Bed']").value;
    const mealType = document.querySelector("select[name='Meal']").value;
    const noOfRoom = parseInt(document.querySelector("select[name='NoofRoom']").value) || 1;

    const cin = new Date(document.querySelector("input[name='cin']").value);
    const cout = new Date(document.querySelector("input[name='cout']").value);

    let nights = 1;
    if (cin && cout && cout > cin) {
        nights = Math.ceil((cout - cin) / (1000 * 60 * 60 * 24));
    }

    const total =
        (roomPrices[roomType] || 0) +
        (mealPrices[mealType] || 0) +
        (bedPrices[bedType] || 0);

    document.getElementById("totalPrice").innerText = "₹" + (total * noOfRoom * nights);
}

document.querySelectorAll("select, input").forEach(el=>{
    el.addEventListener("change", calculatePrice);
});
</script>

</body>
</html>