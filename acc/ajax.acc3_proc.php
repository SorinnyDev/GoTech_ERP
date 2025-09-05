<?
include_once('./_common.php');
$return = array();
$return['ret_code'] = true;
$return['message'] = "";
if(!$mode){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.";
	echo json_encode($return,JSON_UNESCAPED_UNICODE );
	exit;
}

if(count((array)$seq_arr) == 0){
	$return['ret_code'] = false;
	$return['message'] = "선택한 주문이 없습니다.";
	echo json_encode($return,JSON_UNESCAPED_UNICODE );
	exit;
}
$total = 0;
$succ = 0;
$fail = 0;
if($mode == "OK"){ // 정산 완료 처리
	$title = "정산완료";
	for($i = 0 ; $i<count($seq_arr) ; $i++){
		$sql = "UPDATE g5_sales0_list SET wr_cal_chk='Y' WHERE seq='".$seq_arr[$i]."'";
		$rs = sql_query($sql);
		$total++;
		if($rs){
			$succ++;
		}else{
			$fail++;
		}
	}
}else if($mode == "CANCEL"){ // 정산 취소 처리
	$title = "정산취소";
	for($i = 0 ; $i<count($seq_arr) ; $i++){
		$sql = "UPDATE g5_sales0_list SET wr_cal_chk='N' WHERE seq='".$seq_arr[$i]."'";
		$rs = sql_query($sql);
		$total++;
		if($rs){
			$succ++;
		}else{
			$fail++;
		}
	}
}
$msg = $title."(전체:".$total." / 성공:".$succ." / 실패:".$fail.")";
$return['message'] = $msg;
echo json_encode($return,JSON_UNESCAPED_UNICODE );
exit;
?>