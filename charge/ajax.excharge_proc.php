<?
include_once("./_common.php");

$result = array();
$result['ret_code'] = true;
$result['message'] = "";

if(!$mode){
	$result['ret_code'] = false;
	$result['message'] = "필수값이 누락되었습니다.[mode]";
	echo json_encode($result);
	exit;
}

if(!$ex_date){
	$result['ret_code'] = false;
	$result['message'] = "필수값이 누락되었습니다.[ex_date]";
	echo json_encode($result);
	exit;
}

# 트랜잭션 시작
sql_trans_start();

if($mode == "unit"){
	if($ex_date == date("Y-m-d")){
		$table = "g5_excharge";
	}else{
		$table = "g5_excharge_log";
	}

	$sql = "UPDATE ".$table." SET \n";
	$sql .= "	rate='".$rate."'";
	$sql .= "WHERE ex_date='".$ex_date."' AND ex_eng='".$ex_eng."'";
	$rs = sql_query($sql);
	if($rs){
		sql_trans_commit();
		$result['ret_code'] = true;
		$result['message'] = "환율 수정이 완료되었습니다.";
		echo json_encode($result);
		exit;
	}else{
		sql_trans_rollback();
		$result['ret_code'] = false;
		$result['message'] = "환율 수정이 실패하였습니다.";
		echo json_encode($result);
		exit;
	}
}else if($mode == "all"){
	
	if($ex_date == date("Y-m-d")){
		$table = "g5_excharge";
	}else{
		$table = "g5_excharge_log";
	}

	$db_flag = true;

	foreach($ex_eng_arr as $k => $v ){
		$ex_eng = $v;
		$rate = $rate_arr[$v];

		$sql = "UPDATE ".$table." SET \n";
		$sql .= "	rate='".$rate."'";
		$sql .= "WHERE ex_date='".$ex_date."' AND ex_eng='".$ex_eng."'";
		$rs = sql_query($sql);
		if(!$rs){
			$db_flag = false;
		}
	}

	if($db_flag == true){
		sql_trans_commit();
		$result['ret_code'] = true;
		$result['message'] = "환율 수정이 완료되었습니다.";
		echo json_encode($result);
		exit;
	}else{
		sql_trans_rollback();
		$result['ret_code'] = false;
		$result['message'] = "환율 수정이 실패하였습니다.";
		echo json_encode($result);
		exit;
	}
}

?>