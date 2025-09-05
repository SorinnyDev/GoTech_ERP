<?php
include_once('./_common.php');
if ($is_guest) alert('로그인 후 이용하세요.');

if($mode == "add") {
	if(!$_POST['wr_name']) alert('잘못 된 접근입니다.');
	$wr_name = trim($_POST['wr_name']);
	
	$num = sql_fetch("select MAX(wr_code) as code from g5_delivery_company");
	
	$wr_code = (int)$num['code'] + 1;
	
	$sql = "insert into g5_delivery_company set
	wr_code = '{$wr_code}',
	wr_name = '{$wr_name}',
	wr_percent = '{$wr_percent}',
	wr_use = '{$wr_use}'
	";
	sql_query($sql);
	
	
	alert('배송사가 추가되었습니다.');
	
} else if($mode == "mod") {
	
	if(!$_POST['wr_name'] || !$_POST['wr_code']) die('n');
	$wr_name = trim($_POST['wr_name']);
	
	$sql = "update g5_delivery_company set 
	wr_name = '{$wr_name}',
	wr_percent = '{$wr_percent}',
	wr_use = '{$wr_use}'
	where wr_code = '{$wr_code}'
	";
	sql_query($sql);
	
	die('y');
	
} else if($mode == "del") {
	
	if(!$_POST['wr_code']) die('n');
	
	$sql = "delete from g5_delivery_company where wr_code = '{$wr_code}'";
	sql_query($sql);
	
	die('y');
	
}