<?php
if( !isset($_SESSION['expiry']) || $_SESSION['expiry'] < time() ) {
	unset($_SESSION["expiry"]);
	unset($_SESSION["last_login"]);
	header("location: login.php");
	die();
}
$_SESSION["expiry"] = time() + 60 * 35;