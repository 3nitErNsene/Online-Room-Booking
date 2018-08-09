<?php
session_start();
unset($_SESSION["expiry"]);
unset($_SESSION["last_login"]);
header("location:login.php");
die();