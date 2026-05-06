<?php
include 'config.php';
session_start();

if (!isset($_SESSION['usermail'])) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SeaVibe Resort</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ===== THEME ===== */
:root {
    --bg: #ffffff;
    --text: #111;
    --card: #ffffff;
}

.dark {
    --bg: #0b1c2c;
    --text: #ffffff;
    --card: #132c3f;
}

body {
    background: var(--bg);
    color: var(--text);
    margin: 0;
    font-family: 'Poppins', sans-serif;
    transition: 0.3s;
}

/* ===== NAV ===== */
nav {
    position: fixed;
    width: 100%;
    padding: 12px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(12px);
    background: rgba(0,0,0,0.3);
    z-index: 1000;
}

nav .logo {
    color: white;
    font-size: 22px;
    font-weight: 600;
}

nav ul {
    display: flex;
    gap: 20px;
    list-style: none;
    align-items: center;
    margin: 0;
}

nav ul li a {
    color: white;
    text-decoration: none;
}

.nav-btn {
    border-radius: 20px;
    padding: 5px 12px;
}

.logo img {
    height: 40px;   /* main fix */
    width: auto;
    object-fit: contain;
}

/* ===== HERO ===== */
.hero {
    height: 100vh;
    position: relative;
    overflow: hidden;
}

/* Images */
.hero img {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    animation: fadeZoom 20s infinite;
}

/* 5 images timing */
.hero img:nth-child(1) { animation-delay: 0s; }
.hero img:nth-child(2) { animation-delay: 4s; }
.hero img:nth-child(3) { animation-delay: 8s; }
.hero img:nth-child(4) { animation-delay: 12s; }
.hero img:nth-child(5) { animation-delay: 16s; }

/* Animation */
@keyframes fadeZoom {
    0% { opacity: 0; transform: scale(1); }
    5% { opacity: 1; }
    25% { opacity: 1; transform: scale(1.08); }
    30% { opacity: 0; }
    100% { opacity: 0; }
}

/* Overlay */
.hero::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,40,80,0.7), rgba(0,0,0,0.2));
}

/* Text */
.hero-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-align: center;
}

.hero-text h1 {
    font-size: 4rem;
    font-weight: 600;
}

.hero-text p {
    font-size: 1.2rem;
}

/* ===== SECTION ===== */
.section {
    padding:60px 60px;
}

/* ===== ROOMS ===== */
.rooms {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}

.room-card {
    background:var(--card);
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    transition:0.3s;
}

.room-card:hover {
    transform:translateY(-6px);
}

.room-card img {
    width:100%;
    height:200px;
    object-fit:cover;
}

.room-info {
    padding:15px;
}

.book-btn {
    width:100%;
    border-radius:10px;
}

/* ===== FACILITIES ===== */
.facilities {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:25px;
}

.fac-card {
    height:200px;
    border-radius:18px;
    overflow:hidden;
    position:relative;
}

.fac-card img {
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.5s;
}

.fac-card::after {
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:linear-gradient(to top, rgba(0,0,0,0.7), transparent);
}

.fac-card span {
    position:absolute;
    bottom:15px;
    left:15px;
    color:white;
    font-weight:600;
    font-size:18px;
}

.fac-card:hover img {
    transform:scale(1.1);
}

/* ===== CONTACTS ===== */
.contact {
    background:#001f3f;
    color:white;
    padding:30px 20px;
    text-align:center;
}

.contact-row {
    display:flex;
    justify-content:center;
    gap:30px;
    flex-wrap:wrap;
    font-size:14px;
}

/* ===== TOGGLE ===== */
.theme-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 55px;
    height: 30px;
    background: #e0e0e0;
    border-radius: 50px;
    cursor: pointer;
    display:flex;
    align-items:center;
    padding:4px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 9999;
}

.toggle-circle {
    width: 22px;
    height: 22px;
    background: #111;
    border-radius: 50%;
    transition: 0.3s;
}

.dark .theme-toggle {
    background: #333;
}

.dark .toggle-circle {
    transform: translateX(25px);
    background:#fff;
}

</style>
</head>

<body>

<!-- NAV -->
<nav>
    <div class="logo">🌊 SeaVibe</div>
    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#rooms">Rooms</a></li>
        <li><a href="mybookings.php">Booking</a></li>
        <li><a href="#fac">Facilities</a></li>
        <li><a href="#contact">Contacts</a></li>
        <li><a href="logout.php"><button class="btn btn-danger nav-btn">Logout</button></a></li>
    </ul>
</nav>

<!-- HERO -->
<div class="hero">
    <img src="./image/hotel1.jpg" alt="">
    <img src="./image/hotel2.jpg" alt="">
    <img src="./image/hotel3.jpg" alt="">
    <img src="./image/hotel4.jpg" alt="">
    <img src="./image/hotel5.jpg" alt="">

    <div class="hero-text">
        <h1>SeaVibe Resort</h1>
        <p>Feel the Ocean. Live the Luxury.</p>
    </div>
</div>

<!-- ROOMS -->
<section id="rooms" class="section">
<h2 class="text-center mb-4">Our Rooms</h2>

<div class="rooms">
<div class="room-card"><img src="./image/room1.jpg"><div class="room-info"><h5>Superior Room</h5><a href="hm.php" class="btn btn-primary book-btn">Book Now</a></div></div>
<div class="room-card"><img src="./image/room2.jpg"><div class="room-info"><h5>Deluxe Room</h5><a href="hm.php" class="btn btn-primary book-btn">Book Now</a></div></div>
<div class="room-card"><img src="./image/room3.jpg"><div class="room-info"><h5>Guest Room</h5><a href="hm.php" class="btn btn-primary book-btn">Book Now</a></div></div>
<div class="room-card"><img src="./image/room4.jpg"><div class="room-info"><h5>Single Room</h5><a href="hm.php" class="btn btn-primary book-btn">Book Now</a></div></div>
</div>
</section>

<!-- FACILITIES -->
<section id="fac" class="section">
<h2 class="text-center mb-4">Luxury Facilities</h2>

<div class="facilities">
<div class="fac-card"><img src="./image/f1.jpg"><span>Infinity Pool</span></div>
<div class="fac-card"><img src="./image/f2.jpg"><span>Private Beach</span></div>
<div class="fac-card"><img src="./image/f3.jpg"><span>Spa & Wellness</span></div>
<div class="fac-card"><img src="./image/f4.jpg"><span>Fine Dining</span></div>
<div class="fac-card"><img src="./image/f5.jpg"><span>Ocean Gym</span></div>
<div class="fac-card"><img src="./image/f6.jpg"><span>Bar Lounge</span></div>
<div class="fac-card"><img src="./image/f7.jpg"><span>Jet Ski</span></div>
<div class="fac-card"><img src="./image/f8.jpg"><span>Sunset Deck</span></div>
</div>
</section>

<!-- CONTACTS -->
<div id="contact" class="contact">
    <h4>Contacts</h4>
    <div class="contact-row">
        <span>📍 Oceanfront Drive, Coastal Bay</span>
        <span>📞 +00 00000 00000</span>
        <span>✉️ hello@seavibe.demo</span>
    </div>
</div>

<!-- TOGGLE -->
<div class="theme-toggle" onclick="toggleTheme()">
    <div class="toggle-circle"></div>
</div>

<script>
function toggleTheme(){
    document.body.classList.toggle("dark");
    localStorage.setItem("theme",
        document.body.classList.contains("dark") ? "dark":"light");
}

window.onload = () => {
    if(localStorage.getItem("theme") === "dark"){
        document.body.classList.add("dark");
    }
}
</script>

</body>
</html>