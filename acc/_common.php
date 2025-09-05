<?php 
include_once('../common.php');

if($_SERVER['PHP_SELF'] == "/acc/acc1.php" or $_SERVER['PHP_SELF'] == "/acc/acc3.php" or $_SERVER['PHP_SELF'] == "/acc/acc2.php" ){
	$menu_num = 4;
}else{
	$menu_num = 11;
}

if($is_guest) alert('로그인 후 이용바랍니다.', '/bbs/login.php');