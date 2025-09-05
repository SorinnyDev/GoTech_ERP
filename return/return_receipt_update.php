<?php 
include_once('./_common.php');

$mode = trim($_POST['mode']);
if(!$mode) die('n');


if($mode == "update") {
	
	$seq = (int)$_POST['seq'];
	
	if(!$seq) die('n');
	
	$sql = "update g5_return_list set 
	wr_state = 1,
	wr_state_date = '".G5_TIME_YMDHIS."'
	
	where seq = '{$seq}'
	";

	if(sql_query($sql)){
		die('y');
	} else {
		die('nn');
	}
} else if($mode == "delete") {
	
	$count = (isset($_POST['seq']) && is_array($_POST['seq'])) ? count($_POST['seq']) : 0;

	if(!$count) {
		alert('삭제 하실 항목을 하나 이상 선택하세요.');
	}
	
	for($i=0; $i<$count; $i++) {
		
		sql_query("delete from g5_return_list where seq = '{$_POST['seq'][$i]}'");
		
	}
	
	alert('총 '.number_format($count).' 반품등록 건이 삭제되었습니다.');
}