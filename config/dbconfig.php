<?php 

$dsn = 'mysql:dbname=rm_booking;host=localhost;charset=UTF8';
$dbuser = 'root';
$dbpwd = '';

try {
	$pdo = new PDO($dsn, $dbuser, $dbpwd);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die($e->getMessage());
}