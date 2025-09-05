<?php
include_once('./_common.php');

//if(!$member){
//    alert("로그인 후 이용해주세요.");
//    exit;
//}
//
//if(count($_POST['chk'])==0){
//    alert("선택된 목록이 없습니다.");
//    exit;
//}

if(count((array)$seq_arr) == 0){
	alert("선택된 목록이 없습니다.");
	exit;
}

# 파라메터 받기
$mode = $btn_submit;

$succ = 0;
$fail = 0;

if($mode == "선택정산완료"){
	foreach($seq_arr as $k => $v){
		$sql = "UPDATE g5_sales3_list SET wr_cal_chk='Y' WHERE seq='".$v."'";
		sql_query($sql);
	}
	$alert_msg = "정산 처리가 완료되었습니다.";
}else if($mode == "선택정산취소"){
	foreach($seq_arr as $k => $v){
		$sql = "UPDATE g5_sales3_list SET wr_cal_chk='N' WHERE seq='".$v."'";
		sql_query($sql);
	}
	$alert_msg = "정상 취소 처리가 완료되었습니다.";
}

alert($alert_msg);

?>