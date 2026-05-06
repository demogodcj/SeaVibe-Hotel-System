<?php
include 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SeaVibe Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

body{
    height:100vh;
    overflow:hidden;
}

/* BACKGROUND */
.bg{
    position:fixed;
    width:100%;
    height:100%;
    z-index:-2;
}

.bg img{
    position:absolute;
    width:100%;
    height:100%;
    object-fit:cover;
    opacity:0;
    animation:fade 25s infinite;
}

.bg img:nth-child(1){animation-delay:0s;}
.bg img:nth-child(2){animation-delay:5s;}
.bg img:nth-child(3){animation-delay:10s;}
.bg img:nth-child(4){animation-delay:15s;}
.bg img:nth-child(5){animation-delay:20s;}

@keyframes fade{
    0%{opacity:0;}
    10%{opacity:1;}
    30%{opacity:1;}
    40%{opacity:0;}
    100%{opacity:0;}
}

/* OVERLAY */
body::after{
    content:"";
    position:fixed;
    width:100%;
    height:100%;
    background: linear-gradient(135deg, rgba(0,60,120,0.55), rgba(0,120,180,0.35));
    z-index:-1;
}

/* CENTER */
.wrapper{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.card{
    width:380px;
    padding:25px;
    border-radius:18px;
    background: rgba(255,255,255,0.95);
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
}

/* LOGO */
.logo{
    text-align:center;
    margin-bottom:10px;
}

.logo img{
    width:170px;
    object-fit:contain;
}

/* TOGGLE */
.toggle{
    display:flex;
    margin:15px 0;
    border-radius:50px;
    overflow:hidden;
    border:1px solid #cfe8ff;
    background:#eaf6ff;
}

.toggle button{
    flex:1;
    padding:10px;
    border:none;
    cursor:pointer;
    background:transparent;
    font-weight:600;
    color:#0b3d66;
}

.toggle .active{
    background:#0077b6;
    color:white;
}

/* FORMS */
form{display:none;}
form.active{display:block;}

/* INPUT */
input{
    width:100%;
    padding:11px;
    margin:7px 0;
    border-radius:10px;
    border:1px solid #cde7ff;
}

input:focus{
    outline:none;
    border-color:#0077b6;
    box-shadow:0 0 0 3px rgba(0,119,182,0.2);
}

/* BUTTON */
button.submit{
    width:100%;
    padding:11px;
    background:#0077b6;
    color:white;
    border:none;
    border-radius:10px;
    margin-top:10px;
    font-weight:600;
    cursor:pointer;
}

button.submit:hover{
    background:#023e8a;
}

/* SWITCH */
.switch{
    text-align:center;
    margin-top:12px;
    font-size:14px;
    color:#0077b6;
    cursor:pointer;
}

.switch:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<!-- BACKGROUND -->
<div class="bg">
    <img src="./image/hotel1.jpg">
    <img src="./image/hotel2.jpg">
    <img src="./image/hotel3.jpg">
    <img src="./image/hotel4.jpg">
    <img src="./image/hotel5.jpg">
</div>

<div class="wrapper">
<div class="card">

    <div class="logo">
        <img src="./image/SeaVibelogo.png">
    </div>

    <div class="toggle">
        <button id="userBtn" class="active" onclick="showUser()">User</button>
        <button id="staffBtn" onclick="showStaff()">Staff</button>
    </div>

<?php
// ================= USER LOGIN =================
if (isset($_POST['user_login_submit'])) {

    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $sql = "SELECT * FROM signup WHERE Email='$email' AND Password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['usermail'] = $email;
        header("Location: home.php");
        exit();
    } else {
        echo "<script>alert('Invalid User Login');</script>";
    }
}

// ================= STAFF LOGIN (FIXED) =================
if (isset($_POST['Emp_login_submit'])) {

    $email = $_POST['Emp_Email'];
    $password = $_POST['Emp_Password'];

    $sql = "SELECT * FROM emp_login WHERE Emp_Email='$email' AND Emp_Password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['staffmail'] = $email;

        // FIXED PATH (NO FULL URL)
        header("Location: admin/admin.php");
        exit();
    } else {
        echo "<script>alert('Invalid Staff Login');</script>";
    }
}

// ================= SIGNUP =================
if (isset($_POST['user_signup_submit'])) {

    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $cpassword = $_POST['CPassword'];

    if ($password == $cpassword) {

        $check = mysqli_query($conn, "SELECT * FROM signup WHERE Email='$email'");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email already exists');</script>";
        } else {

            mysqli_query($conn, "INSERT INTO signup (Username, Email, Password) 
                                VALUES('$username','$email','$password')");

            $_SESSION['usermail'] = $email;
            header("Location: home.php");
            exit();
        }

    } else {
        echo "<script>alert('Passwords not match');</script>";
    }
}
?>

<!-- USER -->
<form id="userForm" class="active" method="POST">
    <input type="text" name="Username" placeholder="Username" required>
    <input type="email" name="Email" placeholder="Email" required>
    <input type="password" name="Password" placeholder="Password" required>

    <button class="submit" name="user_login_submit">Login</button>
    <div class="switch" onclick="showSignup()">Create account</div>
</form>

<!-- STAFF -->
<form id="staffForm" method="POST">
    <input type="email" name="Emp_Email" placeholder="Staff Email" required>
    <input type="password" name="Emp_Password" placeholder="Password" required>

    <button class="submit" name="Emp_login_submit">Staff Login</button>
</form>

<!-- SIGNUP -->
<form id="signupForm" method="POST">
    <input type="text" name="Username" placeholder="Username" required>
    <input type="email" name="Email" placeholder="Email" required>
    <input type="password" name="Password" placeholder="Password" required>
    <input type="password" name="CPassword" placeholder="Confirm Password" required>

    <button class="submit" name="user_signup_submit">Create Account</button>
    <div class="switch" onclick="showLogin()">Back to Login</div>
</form>

</div>
</div>

<script>
function showUser(){
    userForm.classList.add("active");
    staffForm.classList.remove("active");
    signupForm.classList.remove("active");

    userBtn.classList.add("active");
    staffBtn.classList.remove("active");
}

function showStaff(){
    staffForm.classList.add("active");
    userForm.classList.remove("active");
    signupForm.classList.remove("active");

    staffBtn.classList.add("active");
    userBtn.classList.remove("active");
}

function showSignup(){
    signupForm.classList.add("active");
    userForm.classList.remove("active");
    staffForm.classList.remove("active");
}

function showLogin(){
    showUser();
}
</script>

</body>
</html>