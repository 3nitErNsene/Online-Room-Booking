<?php
// function genPwd($pwd){
//     $com_str = $item."dnspanel";
//     $new_str = base64_encode($com_str);
//     return $new_str;
// }
// function viewPwd($item){
//     $de_str = base64_decode($item);
//     $password = substr($de_str,0,  strlen($de_str)-8);
//     return $password;
// }

function chk_date($item){
	return preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $item);
}

function chk_time($item){
	return preg_match("/^[0-1][0-9]\:[0|3][0]$/", $item);
}

function chk_title($item){
	return preg_replace("/[^ a-zA-Z0-9_-]+/","", $item);
}

function loginSet(){
	$time = date("d/m/Y H:i:s");
	$user = ['user' => $_SESSION['staff_id'], 'last_login' => $time];
	$user = serialize($user);
	setcookie('user', $user, time()+3600*12);
}