<?php 
session_start();
session_destroy();

header("Location: /seavibe/index.php");
exit();
?>