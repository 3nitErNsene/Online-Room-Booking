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
			}?>
			<form>
				<table class="search" cellpadding="7">
					<tr class="search_tr">
						<td class="search_search" colspan="2">Search</td>
						<td>
							&nbsp;
						</td>
						<td style="text-align: right;">
							<button type="button" name="submit_search" class="btn">GO!</button>
						</td>
					</tr>
					<tr class="search_tr">
						<td class="search_td">Room</td>
						<td class="search_td">
							<input type="text" name="room" list="room_list">
							<datalist id="room_list">
								<?php
								$sql_room = "select * from room where not status='c' order by room asc";
								$stmt_room = $pdo->prepare($sql_room);
								$stmt_room->execute();
								while($result=$stmt_room->fetch()){
									echo "<option value='".$result['room']."'>";
								}
								?>
							</datalist>
						</td>
						<td class="search_td">Date</td>
						<td class="search_td">
							<input type="text" name="date" id="datepick" readonly="readonly">
						</td>
					</tr>
				</table>
			</form>

			<div class="result">
				<table class="t_table hide">
					<tr class="event_row">
						<td class="type_t">Time</td>
						<td class="type">Detail</td>
					</tr>
					<?php
						$max_h = 19;
						$min_h = 9;
						for($i=$min_h; $i<$max_h; $i++){
							echo "<tr class='event_row'>";
							echo "<td class='tb_time'>".$i.":00-".$i.":30</td>";
							echo "<td class='event'>&nbsp;</td>";		
							echo "</tr>";
							$next_h = $i+1;
							echo "<tr class='event_row'>";
							echo "<td class='tb_time'>".$i.":30-".$next_h.":00</td>";
							echo "<td class='event'>&nbsp;</td>";		
							echo "</tr>";				
						}

					?>
				</table>
			</div>
		</div>
	</div>
	<script src="js/jquery-1.12.4.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.timepicker.min.js"></script>
	<script src="js/common.js"></script>
	<script>
		$("button").click(function(){
			var room="";
			var date="";
			var subnum = 8;
			//Get input values
			$("input").each(function(){
				if($(this).attr('name')=="room"){
					room = $(this).val();
				}else if($(this).attr('name')=="date"){
					date = $(this).val();
				}
			});

			if(room=="" && date==""){
				alert("Missing search value(s)!");
			}else if(room!="" && date==""){
				alert("Cannot miss Date value(s)!");
			}else{
				var dataString="room="+room+"&date="+date;
				$.ajax({
					type: "POST",
					url: "search.php",
					data: dataString,
					dataType: "json",
					cache: false,
					success: function(record){
						//Show error message or show booking table
						if (record['e'] !== undefined){
							alert(record['e']);
						}else{
							$(".t_table").removeClass("hide");
							if(record.length!=0){
								console.log("result get");
								//search both room and date
								if(room!="" && date!=""){
									//display layout
									$(".result .t_table .event_row .type").each(function(){
										if($(this).length){
											$(this).remove();	
										}	
									});
									$(".result .t_table .event_row .event").each(function(){
										if($(this).length){
											$(this).remove();	
										}	
									});
									$(".result .t_table .event_row .type_t").after("<td class='type'>Detail</td>");
									$(".result .t_table .event_row .tb_time").after("<td class='event'>&nbsp;</td>");

									for(var i=0; i<record.length; i++){
										console.log(Object.keys(record[i]).length);

										var msg = record[i].title+"<br/>"+record[i].login_name+"<br/>"+"("+record[i].start_t+"-"+record[i].end_t+")";
										var s_hr = (record[i].start_t).split(":")[0];
										var s_min = (record[i].start_t).split(":")[1];
										var e_hr = (record[i].end_t).split(":")[0];
										var e_min = (record[i].end_t).split(":")[1];

										var s_index = parseInt(s_hr)-subnum+(parseInt(s_hr)-subnum-2);
										if(s_min=="30"){
											s_index++;
										}

										var e_index = parseInt(e_hr)-subnum+(parseInt(e_hr)-subnum-2);
										if(e_min=="00"){
											e_index--;
										}

										//show detail
										if(s_index == e_index){
											$(".event:eq("+s_index+")").html(msg);								
										}else{
											var rows = e_index-s_index+1;
											$(".event:eq("+s_index+")").attr('rowspan',rows).html(msg);
											for(var hrow=e_index; hrow>s_index; hrow--){
												$(".event:eq("+hrow+")").hide();	
											}
										}
									}
								//search only date
								}else if(room=="" && date!=""){
									if(record[record.length-1].length!=0){
										console.log(record[record.length-1]);
										var count_rooms = Object.keys(record[record.length-1]).length;									
										//display layout
										$(".result .t_table .event_row .type").each(function(){
											if($(this).length){
												$(this).remove();	
											}	
										});
										$(".result .t_table .event_row .event").each(function(){
											if($(this).length){
												$(this).remove();	
											}	
										});
										for(var ikey in record[record.length-1]){
											console.log("count");
											$(".result .t_table .event_row .type_t").after("<td class='type' room='"+record[record.length-1][ikey]+"'>"+record[record.length-1][ikey]+"</td>");
											$(".result .t_table .event_row .tb_time").after("<td class='event'>&nbsp;</td>");
										}
										
										for(var i=0; i<record.length-1; i++){
											console.log(record[i]);
											console.log(record[i].room);
											var msg = record[i].title+"<br/>"+record[i].login_name+"<br/>"+"("+record[i].start_t+"-"+record[i].end_t+")";
											var s_hr = (record[i].start_t).split(":")[0];
											var s_min = (record[i].start_t).split(":")[1];
											var e_hr = (record[i].end_t).split(":")[0];
											var e_min = (record[i].end_t).split(":")[1];

											var s_index = parseInt(s_hr)-subnum+(parseInt(s_hr)-subnum-2);
											if(s_min=="30"){
												s_index++;
											}

											var e_index = parseInt(e_hr)-subnum+(parseInt(e_hr)-subnum-2);
											if(e_min=="00"){
												e_index--;
											}
											
											var rm_index = 0;
											$(".result .t_table .event_row .type").each(function(){
												if($(this).attr('room')==record[i].room){
													rm_index = $(this).index();
												}
											});	
											var index_num_s = rm_index + count_rooms*s_index-1;
											var index_num_e = rm_index + count_rooms*e_index-1;
											console.log(index_num_s);
											console.log(index_num_e);

											if(index_num_s == index_num_e){
												$(".event:eq("+index_num_s+")").html(msg);								
											}else{
												if(count_rooms==1){
													var rows = Math.floor((index_num_e-index_num_s+1)/count_rooms);
												}else{
													var rows = Math.floor((index_num_e-index_num_s+1)/count_rooms) + 1;
												}
													$(".event:eq("+index_num_s+")").attr('rowspan',rows).html(msg);
													for(var hrow=index_num_s+count_rooms; hrow<=index_num_e; hrow+=count_rooms){
														$(".event:eq("+hrow+")").hide();	
													}
													console.log(rows);
											}					
										}
									}
								}
							}else{
								console.log("no result");
								$(".result .t_table .event_row .type").each(function(){
									if($(this).length){
										$(this).remove();	
									}	
								});
								$(".result .t_table .event_row .event").each(function(){
									if($(this).length){
										$(this).remove();	
									}	
								});
								$(".result .t_table .event_row .type_t").after("<td class='type'>Detail</td>");
								$(".result .t_table .event_row .tb_time").after("<td class='event'>&nbsp;</td>");
							}
						}
					}
				});
			}
		});
	</script>
</body>
</html>