<?php
// Destroy cookie
setcookie("abcdefdryz", "", time() - 3600, "/");

// Destroy session if exists
if (session_status() === PHP_SESSION_NONE) session_start();
session_unset();
session_destroy();

header("Location: ../redrr/?login");
exit();