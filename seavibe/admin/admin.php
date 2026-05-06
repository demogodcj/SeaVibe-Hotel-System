<?php
include '../config.php';
session_start();

/* STAFF LOGIN CHECK */
$staffmail = $_SESSION['staffmail'] ?? null;

if (!$staffmail) {
    header("Location: http://localhost/seavibe/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SeaVibe Admin</title>

<link rel="stylesheet" href="./css/admin.css">
<link rel="stylesheet" href="../css/flash.css">

<script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<style>

/* ===== THEME ===== */
:root{
    --blue1:#0a3d62;
    --blue2:#1289a7;
    --bg:#f5fbff;
    --card:#ffffff;
    --text:#0b2239;
}

/* ===== GLOBAL ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: "Segoe UI", sans-serif;
}

body{
    background:var(--bg);
    color:var(--text);
    overflow:hidden;
}

/* ===== TOP NAV ===== */
.uppernav{
    height:65px;
    width:100%;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(135deg,var(--blue1),var(--blue2));
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 25px;
    z-index:1000;
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
}

.logo{
    display:flex;
    align-items:center;
    gap:10px;
    color:white;
    font-weight:600;
}

.bluebirdlogo{
    height:35px;
}

.logout button{
    background:#fff;
    color:var(--blue1);
    border:none;
    padding:7px 15px;
    border-radius:20px;
    font-weight:600;
    cursor:pointer;
}

/* ===== SIDEBAR ===== */
.sidenav{
    position:fixed;
    top:65px;
    left:0;
    width:240px;
    height:100%;
    background:#ffffff;
    border-right:1px solid #e6f0ff;
}

.sidenav ul{
    list-style:none;
}

.sidenav li{
    padding:14px 20px;
    margin:6px 10px;
    border-radius:10px;
    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
    color:#1b3b5f;
}

.sidenav li:hover{
    background:#eaf6ff;
}

.sidenav .active{
    background:linear-gradient(135deg,var(--blue1),var(--blue2));
    color:white;
}

/* ===== MAIN ===== */
.mainscreen{
    margin-left:240px;
    margin-top:65px;
    height:calc(100vh - 65px);
}

.frames{
    width:100%;
    height:100%;
    border:none;
    display:none;
}

.frames.active{
    display:block;
}

/* MOBILE */
@media(max-width:900px){
    #mobileview{
        display:flex;
    }
    .sidenav,.uppernav,.mainscreen{
        display:none;
    }
}

</style>
</head>

<body>

<div id="mobileview">
    <h3>Admin panel is not available on mobile view</h3>
</div>

<!-- TOP NAV -->
<div class="uppernav">
    <div class="logo">
        <img class="bluebirdlogo" src="../image/bluebirdlogo.png">
        <span>SeaVibe Admin</span>
    </div>

    <div class="logout">
        <a href="../logout.php">
            <button><i class="fa fa-sign-out-alt"></i> Logout</button>
        </a>
    </div>
</div>

<!-- SIDEBAR -->
<nav class="sidenav">
    <ul>
        <li class="pagebtn active"><img src="../image/icon/dashboard.png"> Dashboard</li>
        <li class="pagebtn"><img src="../image/icon/bed.png"> Room Booking</li>
        <li class="pagebtn"><img src="../image/icon/wallet.png"> Payment</li>
        <li class="pagebtn"><img src="../image/icon/bedroom.png"> Rooms</li>
        <li class="pagebtn"><img src="../image/icon/staff.png"> Staff</li>
        <li class="pagebtn"><i class="fa fa-ban"></i> Cancellations</li>
    </ul>
</nav>

<!-- MAIN -->
<div class="mainscreen">

    <iframe class="frames frame1 active" src="./dashboard.php"></iframe>

    <iframe class="frames frame2" src="./roombook.php"></iframe>

    <iframe class="frames frame3" src="./payment.php"></iframe>

    <iframe class="frames frame4" src="./room.php"></iframe>

    <iframe class="frames frame5" src="./staff.php"></iframe>

    <iframe class="frames frame6" src="/seavibe/admin/cancellations.php"></iframe>

</div>

<script src="./javascript/script.js"></script>

</body>
</html>