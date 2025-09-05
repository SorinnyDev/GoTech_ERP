<?php
include_once('./_common.php');

# 반환값
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

# 필수값 체크
if(isDefined($seq) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[seq]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_cal_chk) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[wr_cal_chk]";
	echo json_encode($return);
	exit;
}

$sql = "UPDATE g5_sales3_list SET wr_cal_chk='".$wr_cal_chk."' WHERE seq='".$seq."'";
$rs = sql_query($sql);
if($rs){
	$return['ret_code'] = true;
	if($wr_cal_chk == "Y"){

		$sql = "
			INSERT INTO g5_sales3_cal
			SET
					sales3_seq = {$seq},
					fee1 = $fee1_value,
					fee1_status = $fee1_checked,
					fee2 = $fee2_value,
					fee2_status = $fee2_checked,
					tax = $tax_value,
					tax_status = $tax_checked
			ON DUPLICATE KEY UPDATE
					fee1 = VALUES(fee1),
					fee1_status = VALUES(fee1_status),
					fee2 = VALUES(fee2),
					fee2_status = VALUES(fee2_status),
					tax = VALUES(tax),
					tax_status = VALUES(tax_status)
		";		

		sql_query($sql);

		$return['message'] = "정산처리가 완료되었습니다.";
	}else{

		$sql = "delete from g5_sales3_cal where sales3_seq = $seq";
		sql_query($sql);

		$return['message'] = "정산취소가 완료되었습니다.";
	}
	echo json_encode($return);
	exit;
}else{
	$return['ret_code'] = true;
	$return['message'] = "데이터 업데이트에 실패하였습니다.";
	echo json_encode($return);
	exit;
}

?>