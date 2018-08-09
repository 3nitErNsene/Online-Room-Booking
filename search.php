<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
	header("location:login.php");
	die();
}

include("config/dbconfig.php");
include("config/web_config.php");

$error="";

$room = $_POST['room'];
//$room = '';
if($room!=""){
	try{
		$room_match = 0;
		$sql = "select * from room where not status='c'";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		while($result=$stmt->fetch()){
			if ($room == $result['room']){
				$room_match++;
			}
		}
		if ($room_match == 0){
			$error = $error."No room ".$room." exist."."\n";
		}
	}catch(PDOException $e){
		die('DB error');
	}
}

$date = $_POST['date'];
//$date = '2018-07-27';
if($date!=""){
	if (!chk_date($date)){
		$error = $error."Error date input."."\n";
	}	
}

$record = array();
$detail = array(
	'start_t'  => "" ,
	'end_t'   =>  "" ,
	'title'   =>  "" ,
	'login_name'   => ""  
);
$room_detail = array(
	'room' => "",
	'start_t'  => "" ,
	'end_t'   =>  "" ,
	'title'   =>  "" ,
	'login_name'   => ""  
);
$rooms = array();


if($error != ""){
	$record['e'] = $error;
}else{
	if($room!="" && $date!=""){
		try{
			$sql = "select * from booking, staff where booking.staff_id=staff.staff_id and room=:room and bk_day=:bk_day";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(":room", $room);
			$stmt->bindParam(":bk_day", $date);
			$stmt->execute();
			$i=0;
			while($result=$stmt->fetch()){
				$detail['start_t'] = $result['start_t'];
				$detail['end_t'] = $result['end_t'];
				$detail['title'] = $result['title'];
				$detail['login_name'] = $result['login_name'];
				$record[$i] = $detail;
				$i++;
			}
		}catch(PDOException $e){
			die('DB error');
		}
	}else if($room=="" && $date!=""){
		try{
			$sql = "select distinct room from booking where bk_day=:bk_day order by bk_day asc, room desc;";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(":bk_day", $date);
			$stmt->execute();
			while($result=$stmt->fetch()){
				array_push($rooms, $result['room']);
			}

			$sql = "select * from booking, staff where booking.staff_id=staff.staff_id and bk_day=:bk_day order by bk_day asc, room asc";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(":bk_day", $date);
			$stmt->execute();
			$i=0;
			while($result=$stmt->fetch()){
				$room_detail['room'] = $result['room'];
				$room_detail['start_t'] = $result['start_t'];
				$room_detail['end_t'] = $result['end_t'];
				$room_detail['title'] = $result['title'];
				$room_detail['login_name'] = $result['login_name'];
				$record[$i] = $room_detail;
				$i++;
			}
			if(sizeof($rooms)>0){
				$record[$i] = $rooms;
			}
			
		}catch(PDOException $e){
			die('DB error');
		}
	}
}

echo json_encode($record);