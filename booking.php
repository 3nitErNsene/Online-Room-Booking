<body>
<?php
session_start();
if(!isset($_POST["bking_fm_submit"])){
	header("location:login.php");
	die();
}
include("config/chk_login.php");
include("config/dbconfig.php");
include("config/web_config.php");

$error = "";

if (!chk_date($_POST['date'])){
	$error = $error."Error date input."."\\n";
}else{
	$date = $_POST['date'];
}

if (!chk_time($_POST['start_t'])){
	$error = $error."Error start time input."."\\n";
}else{
	$start_t = $_POST['start_t'];
}

if (!chk_time($_POST['end_t'])){
	$error = $error."Error end time input."."\\n";
}else{
	$end_t = $_POST['end_t'];
}

if ($_POST['end_t']<$_POST['start_t']){
	$error = $error."Error start time and End time input."."\\n";
}

if ($_POST['end_t']==$_POST['start_t']){
	$error = $error."Error start time and End time input."."\\n";
}

try{
	$room = $_POST['room'];
	$room_match = 0;
	$sql = "select * from room where not status='c'";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	while($result=$stmt->fetch()){
		if ($room == $result['room']){
			$room_match++;
		}
	}
}catch(PDOException $e){
	die('DB error');
}

if ($room_match == 0){
	$error = $error."No room ".$room." exist."."\\n";
}

$title = chk_title($_POST['title']);

if($error != ""){
	echo "<script>alert('".$error."');";
	echo "window.location = 'main.php';</script>";
}else{
	//check appointment period has been occupied
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
		//confirm room booking
		try{
			$sql_count = "select count(*) from booking";
			$count_stmt = $pdo->prepare($sql_count);
			$count_stmt->execute();
			$id = $count_stmt->fetchColumn() + 1;
			$sql_bking = "insert into booking values(:id, :room, :bk_day, :start_t, :end_t, :staff_id, :title)";
			$stmt = $pdo->prepare($sql_bking);
			$stmt->bindParam(":id", $id);
			$stmt->bindParam(":room", $room);
			$stmt->bindParam(":bk_day", $date);
			$stmt->bindParam(":start_t", $start_t);
			$stmt->bindParam(":end_t", $end_t);
			$stmt->bindParam(":staff_id", $_SESSION['staff_id']);
			$stmt->bindParam(":title", $title);
			$stmt->execute();
			if ($stmt->rowCount()>0){
			 	echo "<script>alert('Booking Confirmed!');";
			 	echo "window.location = 'Calendar.php';</script>";
			}
		}catch(PDOException $e){
			die('DB error');
		}			
	}else{
		if ($stmt->rowCount()>0){
		 	echo "<script>alert('Appointment period has been occupied !');";
		 	echo "window.location = 'Calendar.php';</script>";
		}
	}
}

?>
</body>
