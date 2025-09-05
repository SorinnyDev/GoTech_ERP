<?php 
include_once('./_common.php');

$mode = trim($_POST['mode']);

if($mode == "delete") {

	$count = (isset($_POST['seq']) && is_array($_POST['seq'])) ? count($_POST['seq']) : 0;

	if(!$count) {
		alert('삭제 하실 항목을 하나 이상 선택하세요.');
	}

	for($i=0; $i<$count; $i++) {
		
		sql_query("delete from g5_return_img where seq = '{$_POST['seq'][$i]}'");
		
	}

	alert('총 '.number_format($count).'건의 반품사진이 삭제되었습니다.');

} else {
	alert('잘못 된 접근입니다.');
}