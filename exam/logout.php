<?php
session_start();
session_unset(); // ล้างตัวแปร Session ทั้งหมด
session_destroy(); // ทำลาย Session
header("Location: login.php"); // Redirect กลับไปหน้า Login
exit();
?>
