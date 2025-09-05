<?
include_once('./_common.php');

# 리턴값 배열
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

if(isDefined($seq) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[seq]";
	echo json_encode($return);
	exit;
}else if(!isDefined($mode)){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[mode]";
	echo json_encode($return);
	exit;
}

# 트랜잭션 시작
sql_trans_start();

$pay_price = str_replace(',', '', $pay_price);
$misu = str_replace(',', '', $misu);

if($mode == "wr_orderer"){
	$sql = "UPDATE g5_sales2_list SET wr_warehouse_price = '".$pay_price."', wr_misu = '".$misu."' WHERE seq='".$seq."'";
	$rs = sql_query($sql);
	if($rs){
		$sql = "UPDATE g5_sales3_list SET wr_misu = '".$misu."' WHERE sales2_id='".$seq."'";
		$rs = sql_query($sql);
		if($rs){
			# 트랜잭션 완료
			sql_trans_commit();
			$return['ret_code'] = true;
			$return['message'] = "업데이트가 완료되었습니다.";
			echo json_encode($return);
			exit;
		}else{
			# 트랜잭션 롤백
			sql_trans_rollback();
			$return['ret_code'] = false;
			$return['message'] = "업데이트에 실패하였습니다.[sales3]";
			echo json_encode($return);
			exit;
		}
	}else{
		# 트랜잭션 롤백
		sql_trans_rollback();
		$return['ret_code'] = false;
		$return['message'] = "업데이트에 실패하였습니다.[sales2]";
		echo json_encode($return);
		exit;
	}
}else if($mode == "wr_delivery"){
	$sql = "UPDATE g5_sales3_list SET wr_delivery_pay='".$pay_price."', wr_delivery_misu='".$misu."' WHERE seq='".$seq."'";
	$rs = sql_query($sql);
	if($rs){
		# 트랜잭션 완료
		sql_trans_commit();
		$return['ret_code'] = true;
		$return['message'] = "업데이트가 완료되었습니다.";
		echo json_encode($return);
		exit;
	}else{
		# 트랜잭션 롤백
		sql_trans_rollback();
		$return['ret_code'] = false;
		$return['message'] = "업데이트에 실패하였습니다.[sales3]";
		echo json_encode($return);
		exit;
	}
}

?>