<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
	header("location:login.php");
	die();
}

include("config/dbconfig.php");
include("config/web_config.php");

$id = $_POST['id'];

$record = array();

try{

	$count = 0;

	$sql_count = "select count(*) from booking";
	$count_stmt = $pdo->prepare($sql_count);
	$count_stmt->execute();
	$last_id = $count_stmt->fetchColumn();


	$sql_del = "delete from booking where id=:id";
	$del_stmt = $pdo->prepare($sql_del);
	$del_stmt->bindParam(":id", $id);
	$del_stmt->execute();

	if ($del_stmt->rowCount()>0){
		$count+=2;
	}

	if($id!=$last_id){

		$sql_update = "update booking set id=:id where id=:last_id";
		$update_stmt = $pdo->prepare($sql_update);
		$update_stmt->bindParam(":id", $id);
		$update_stmt->bindParam(":last_id", $last_id);
		$update_stmt->execute();
		
		if ($update_stmt->rowCount()>0){
			$count+=1;
		}
	}

	$record['msg'] = $count;
	echo json_encode($record);

}catch(PDOException $e){
	die('DB error');
}			
