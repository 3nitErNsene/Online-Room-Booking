<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
	header("location:login.php");
	die();
}

include("config/dbconfig.php");
include("config/web_config.php");

$date = $_POST['date'];
$room = $_POST['room'];
$start_t = $_POST['start_t'];
$end_t = $_POST['end_t'];
$id = $_POST['id'];

//$record = array($date,$room,$start_t,$end_t,$id);
$record = array();
$occupied = array();

$sql_chk_s_time = "select count(*) from booking where bk_day=:date and room=:room and ((:start_t>start_t and :start_t<end_t) or start_t=:start_t);";
$sql_chk_e_time = "select count(*) from booking where bk_day=:date and room=:room and ((:end_t>start_t and :end_t<end_t) or end_t=:end_t);";

$s_time_stmt = $pdo->prepare($sql_chk_s_time);
$s_time_stmt->bindParam(":date", $date);
$s_time_stmt->bindParam(":room", $room);
$s_time_stmt->bindParam(":start_t", $start_t);
$s_time_stmt->execute();
$s_time_empty = $s_time_stmt->fetchColumn();

$e_time_stmt = $pdo->prepare($sql_chk_e_time);
$e_time_stmt->bindParam(":date", $date);
$e_time_stmt->bindParam(":room", $room);
$e_time_stmt->bindParam(":end_t", $end_t);
$e_time_stmt->execute();
$e_time_empty = $e_time_stmt->fetchColumn();

if($s_time_empty==0 && $e_time_empty==0){
	//echo json_encode($record);
	//confirm room booking
	try{
		$sql_update = "update booking set bk_day=:date, room=:room, start_t=:start_t, end_t=:end_t where id=:id";
		$update_stmt = $pdo->prepare($sql_update);
		$update_stmt->bindParam(":date", $date);
		$update_stmt->bindParam(":room", $room);
		$update_stmt->bindParam(":start_t", $start_t);
		$update_stmt->bindParam(":end_t", $end_t);
		$update_stmt->bindParam(":id", $id);
		$update_stmt->execute();
		
		if ($update_stmt->rowCount()>0){
			echo json_encode($record);
		}
	}catch(PDOException $e){
		die('DB error');
	}			
}else{
	$occupied['e'] = "Appointment period has been occupied !";
	echo json_encode($occupied);
 	// echo "<script>alert('Appointment period has been occupied !');";
 	// echo "window.location = 'edit.php';</script>";
}