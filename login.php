<!DOCTYPE HTML>
<html>
<head>
	<title>Online Room Booking</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript">
		function formvalid(form){
			var error = "";
			if(form.username.value==""){
				error = error + "Please enter username\n";
			}
			if(form.password.value==""){
				error = error + "Please enter password\n";
			}
			if (error==""){
				return true;
			}else{
				alert(error);
				return false;
			}
		}
	</script>
</head>
<body>
	<div class="pg-content">
		<h4>Online Room Booking System</h4>
		<div class="pg-login">
			<div class="title">Login</div>
			<form action="login_process.php" method="post" onsubmit="return formvalid(this);">
				<div>
					<label>Username: &nbsp;&nbsp;</label>
					<input type="text" name="username" />
				</div>
				<br/>
				<div>
					<label>Password: &nbsp;&nbsp;</label>
					<input type="password" name="password" maxlength="50" />
				</div>
				<input type="submit" name="submit" class="btn" value="Login"/>&nbsp;&nbsp; 
			</form>
		</div>
	</div>
</body>
</html>