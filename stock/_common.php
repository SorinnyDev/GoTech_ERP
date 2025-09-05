<?php 
include_once('../common.php');

if($mode == "restock")
	$menu_num = 5;
else
	$menu_num = 1;
	
if($is_guest) alert('로그인 후 이용바랍니다.', '/bbs/login.php');


?>