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
		<div class="cal_content">
			<?php if($_SESSION['last_login']!==""){
				echo '<div class="time">Last Login : '.$_SESSION['last_login'].'</div>';
				echo '<div class="subject">Your  Booking</div>';
			}else{
				echo '<div class="subject2">Your  Booking</div>';
			}?>
			
			<div class="result">
				<table class="t_table">
					<tr class="event_row">
						<td class="type2">No.</td>
						<td class="type2">Date</td>
						<td class="type2">Room</td>
						<td class="type2">Start Time</td>
						<td class="type2">End Time</td>
						<td class="type2">Title</td>
						<td class="type2">Action</td>
						<td class="type2 hide">ID</td>
					</tr>
					<?php
					$sql = "select bk_day, room, start_t, end_t, title, id from booking where (bk_day>CURDATE() or bk_day=CURDATE()) and staff_id=:staff_id order by bk_day, start_t;";
					try{
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(":staff_id", $_SESSION['staff_id']);
						$stmt->execute();
						$i=1;
						while($result=$stmt->fetch()){
							echo "<tr class='event_row'>";
							echo "<td class='tb_time'>".$i."</td>";
							echo "<td class='tb_time'>".$result['bk_day']."</td>";
							echo "<td class='tb_time'>".$result['room']."</td>";
							echo "<td class='tb_time'>".$result['start_t']."</td>";
							echo "<td class='tb_time'>".$result['end_t']."</td>";
							echo "<td class='tb_time'>".$result['title']."</td>";
							echo "<td class='tb_time'>
									<button type='button' class='actionBtn edit'>Edit</button><button type='button' class='actionBtn del'>Delete</button>
								</td>";
							echo "<td class='tb_time hide'>".$result['id']."</td>";
							echo "</tr>";	
							$i++;
						}
					}catch(PDOException $e){
						die('DB error');
					}

					?>
				</table>
			</div>

			<div class="shape hide"></div>

			<div class="edit_box hide">
				<div class="title">Edit</div>
				<table>
					<tr>
						<td class="sub">Date</td>
						<td><input type="text" name="date" id="datepick" readonly="readonly"></td>
					</tr>
					<tr>
						<td class="sub">Room</td>
						<td>
							<select name="room">
								<?php
								$sql_room = "select * from room where not status='c' order by room asc";
								$stmt_room = $pdo->prepare($sql_room);
								$stmt_room->execute();
								while($result=$stmt_room->fetch()){
									echo "<option value='".$result['room']."'>".$result['room']."</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="sub">Start Time</td>
						<td><input type="text" name="start_t" class="timepick" maxlength="5" required></td>
					</tr>
					<tr>
						<td class="sub">End Time</td>
						<td><input type="text" name="end_t" class="timepick" maxlength="5" required></td>
					</tr>
					<tr>
						<td class="sub hide">ID</td>
						<td><input type="text" class="hide" name="id"/></td>
					</tr>
				</table>
				<div class="action">
					<button type='button' id="exit">Exit</button><button type='button' id="update">Update</button>
					<!--<input type="submit" id="update" name="update" value="update"/>-->
				</div>
			</div>

		</div>
	</div>
	<script src="js/jquery-1.12.4.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.timepicker.min.js"></script>
	<script src="js/common.js"></script>
	<script>
		$(".del").click(function(){
			
		    if (confirm("Sure delete ?") == true) {
		        $(".t_table tr:eq("+$(this).parents("tr").index()+")").addClass("hide");
				console.log($(this).parents("tr").index());

				console.log($(".t_table tr:eq("+$(this).parents("tr").index()+")").children().eq(7).text());

				var dataString="id="+$(".t_table tr:eq("+$(this).parents("tr").index()+")").children().eq(7).text();
				$.ajax({
					type: "POST",
					url: "delete_process.php",
					data: dataString,
					dataType: "json",
					cache: false,
					success: function(record){
						window.location = 'edit.php';
						if (record['msg'] < 1){
							alert(record['msg']);
						}
					}
				});
		    }
		});

		$(".edit").click(function(){
			console.log($(this).parents("tr").index());

			$(".shape").removeClass("hide");
			$(".edit_box").removeClass("hide");

			var pre_val = [];

			$(".t_table tr:eq("+$(this).parents("tr").index()+")").children().each(function(){
				var td_val = $(this).text();
				if($(this).index()==1){
					pre_val["date"] = td_val;
					$(".edit_box input[name='date']").val(td_val);
				}					
				if($(this).index()==2){
					$(".edit_box select option").each(function(){
						$(this).removeAttr("selected");
					});
					pre_val["room"] = td_val;
					$(".edit_box select option[value='"+td_val+"']").attr("selected",true);
				}
				if($(this).index()==3){
					pre_val["start_t"] = td_val;
					$(".edit_box input[name='start_t']").val(td_val);
				}
				if($(this).index()==4){
					pre_val["end_t"] = td_val;
					$(".edit_box input[name='end_t']").val(td_val);
				}
				if($(this).index()==7){
					$(".edit_box input[name='id']").val(td_val);
				}		
			});	

			$("#exit").click(function(){
				$(".shape").addClass("hide");
				$(".edit_box").addClass("hide");
				$("#exit").unbind('click');
			});
			$("#update").click(function(){
				var value = [];
				$('.edit_box input, select').each(function(){
					value[$(this).attr('name')] = $(this).val();
					console.log($(this).attr('name') + "--" + $(this).val());
				});

				//check if change
				if (pre_val["date"]==value["date"] && pre_val["room"]==value["room"] && pre_val["start_t"]==value["start_t"] && pre_val["end_t"]==value["end_t"]){
					alert("Booking not change.");
				}else{
					var dataString="date="+value["date"]+"&room="+value["room"]+"&start_t="+value["start_t"]+"&end_t="+value["end_t"]+"&id="+value["id"];
					console.log(dataString);
					$.ajax({
						type: "POST",
						url: "edit_process.php",
						data: dataString,
						dataType: "json",
						cache: false,
						success: function(record){
							if (record['e'] !== undefined){
								alert(record['e']);
							}else{
								alert("Booking updated!");
								window.location = 'Calendar.php';
								//console.log(record[0] + "+" + record[1] + "+" + record[2] + "+" +record[3] + "+" +record[4]);
							}
						}
					});
				}

				$("#update").unbind('click');
				$(".shape").addClass("hide");
				$(".edit_box").addClass("hide");
			});			
		});
	</script>
</body>
</html>