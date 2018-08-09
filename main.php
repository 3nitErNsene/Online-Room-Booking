<?php
include("config/web_config.php");
date_default_timezone_set('Asia/Hong_Kong');
session_start();

if(!isset($_COOKIE['user'])){
	$_SESSION['last_login'] = "";
	loginSet();
}else{
	if (unserialize($_COOKIE['user'])['user'] == $_SESSION['staff_id']){
		if (!isset($_SESSION['last_login'])){
			$_SESSION['last_login'] = unserialize($_COOKIE['user'])['last_login'];
			loginSet();
		}
	}else{
		$_SESSION['last_login'] = "";
		loginSet();
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Online Room Booking System</title>
	<link rel="stylesheet" type="text/css" href="css/pg_style.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.timepicker.min.css" />
</head>
<body>
<?php
include("config/chk_login.php");
include("config/dbconfig.php");
include("header.php");
include("nav.php")
?>

	<div class="main">
		<?php if($_SESSION['last_login']!==""){
			echo '<div class="time">Last Login : '.$_SESSION['last_login'].'</div>';
			echo '<div class="subject">Room  Booking</div>';
		}else{
			echo '<div class="subject2">Room  Booking</div>';
		}?>
		
		<div class="content">
			<form action="booking.php" method="POST" onsubmit="return formvalid(this);">
				<table cellpadding="13">
					<tr>
						<td class="t_tit">Date</td>
						<td class="t_cont"><input type="text" name="date" id="datepick" readonly="readonly" required></td>
						<td class="t_cont_err hide"></td>
					</tr>
					<tr>
						<td class="t_tit">Start Time</td>
						<td class="t_cont"><input type="text" name="start_t" class="timepick" maxlength="5" required></td>
						<td class="t_cont_err hide"></td>
					</tr>
					<tr>
						<td class="t_tit">End Time</td>
						<td class="t_cont"><input type="text" name="end_t" class="timepick" maxlength="5" required></td>
						<td class="t_cont_err hide"></td>
					</tr>
					<tr>
						<td class="t_tit">Room</td>
						<td class="t_cont"><input type="text" name="room" list="room_list" required>
						<datalist id="room_list">
							<?php
							$sql = "select * from room where not status='c' order by room asc";
							$stmt = $pdo->prepare($sql);
							$stmt->execute();
							while($result=$stmt->fetch()){
								echo "<option value='".$result['room']."'>";
							}
							?>
						</datalist>
						</td>
						<td class="t_cont_err hide"></td>
					</tr>
					<tr>
						<td class="t_tit">Title</td>
						<td class="t_cont"><input type="text" name="title" required></td>
						<td class="t_cont_err hide"></td>
					</tr>
					<tr>
						<td colspan="2"><center><button type="submit" name="bking_fm_submit" class="btn">Confirm</button></center></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.timepicker.min.js"></script>
	<script src="js/common.js"></script>
	<script>
		function formvalid(form){
			var error = "";
			if(form.date.value==""){
				error = "Please enter Date\n";
				$(form.date).parent().next().removeClass("hide").text(error);
			}
			if(form.start_t.value==""){
				error = "Please enter Start Time\n";
				$(form.start_t).parent().next().removeClass("hide").text(error);
			}
			if(form.end_t.value==""){
				error = "Please enter End Time\n";
				$(form.end_t).parent().next().removeClass("hide").text(error);
			}
			if(form.room.value==""){
				error = "Please enter Room\n";
				$(form.room).parent().next().removeClass("hide").text(error);
			}
			if(form.title.value==""){
				error = "Please enter Title\n";
				$(form.title).parent().next().removeClass("hide").text(error);
			}
			if (error==""){
				if(form.start_t.value==form.end_t.value){
					alert("WRONG Start Time and End Time!");
					return false;
				}else if(form.start_t.value>form.end_t.value){
					alert("WRONG Start Time and End Time!");
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}

		// $("form").on("submit", function(){
		// 	$
		// 	return false;
		// });
	</script>
</body>
</html>