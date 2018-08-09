</html>
<body>
<?php
if(!isset($_POST["submit"])){
	header("location:login.php");
	die();
}
date_default_timezone_set('Asia/Hong_Kong');
session_start();
include("config/dbconfig.php");

try{
	$sql = "select * from staff where login_name=:username and pwd=:password";
	$stmt = $pdo->prepare($sql);
	$pass = base64_encode($_POST["password"]);
	$stmt->bindParam(":username", $_POST["username"]);
	$stmt->bindParam(":password", $pass);
	$stmt->execute();
	if($stmt->rowCount()<1){
		header("location:login.php");
	}else{
		$_SESSION["expiry"] = time() + 60*30;

		while($result=$stmt->fetch()){
			$_SESSION["staff_id"] = $result['staff_id'];
			$_SESSION["login_name"] = $result['login_name'];
			$_SESSION["post"] = $result['post']; 
		}

		header("location:main.php");
		die();
	}
}catch(PDOException $e){
	die('DB error');
}

?>
</body>
</html>