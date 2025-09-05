<?
include_once("./_common.php");

$return = array();
$return['ret_code'] = true;
$return['message'] = "";

# 필수값 체크
if(isDefined($wr_id) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[wr_id]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_17) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[상품명]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_16) == false){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[SKU]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_11) == false || $wr_11 == 0){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[수량]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_13) == false || $wr_13 == 0){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[단가]";
	echo json_encode($return);
	exit;
}else if(isDefined($wr_14) == false || $wr_14 == 0){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[신고가격]";
	echo json_encode($return);
	exit;
}


# 매출등록이 되었는지 체크
$sql = "SELECT * FROM g5_sales0_list WHERE wr_id='".$wr_id."'";
$chk = sql_fetch($sql);
if($chk['seq']){
	$return['ret_code'] = false;
	$return['message'] = "매출등록이 완료된 정보입니다.";
	echo json_encode($return);
	exit;
}

# 동일 상품명 및 동일 SKU 데이터 업데이트를 위한 정보 불러오기
$sql = "SELECT * FROM g5_write_sales WHERE wr_id='".$wr_id."'";
$row = sql_fetch($sql);

if(isDefined($wr_16) == true){
	$item = sql_fetch("select wr_id from g5_write_product where (wr_1 = '".addslashes($wr_16)."' or wr_27 = '".addslashes($wr_16)."' or wr_28 = '".addslashes($wr_16)."' or wr_29 = '".addslashes($wr_16)."' or wr_30 = '".addslashes($wr_16)."' or wr_31 = '".addslashes($wr_16)."') AND wr_delYn = 'N'  ");
}else{
	$item['wr_id'] = "0";
}

if(!$item['wr_id']){
	$item['wr_id'] = "0";
}

sql_trans_start();

$sql = "UPDATE g5_write_sales SET \n";
$sql .= "	wr_17 = '".addslashes($wr_17)."' \n";
$sql .= "	,wr_16 = '".addslashes($wr_16)."' \n";
$sql .= "	,wr_11 = '".$wr_11."' \n";
$sql .= "	,wr_12 = '".$wr_12."' \n";
$sql .= "	,wr_13 = '".$wr_13."' \n";
$sql .= "	,wr_14 = '".$wr_14."' \n";
$sql .= "	,wr_22 = '".$wr_22."' \n";
$sql .= "	,wr_23 = '".$wr_23."' \n";
$sql .= "	,wr_35 = '".$wr_35."' \n";
$sql .= "	,wr_36 = '".$wr_36."' \n";
$sql .= "	,wr_product_id = '".$item['wr_id']."' \n";
$sql .= "WHERE wr_id='".$wr_id."'";
$rs = sql_query($sql);
if($rs){
	$sql = "UPDATE g5_write_sales A \n";
	$sql .= "LEFT OUTER JOIN g5_sales0_list B ON B.wr_id=A.wr_id \n";
	$sql .= "SET  \n";
	$sql .= "	A.wr_16='".addslashes($wr_16)."' \n";
	$sql .= "	,A.wr_17='".addslashes($wr_17)."' \n";
	$sql .= "	,A.wr_product_id='".$item['wr_id']."' \n";
	$sql .= "WHERE B.seq IS NULL AND A.wr_16= '".addslashes($row['wr_16'])."' AND A.wr_17 = '".addslashes($row['wr_17'])."'";
	$rs = sql_query($sql);
	if($rs){
		sql_trans_commit();
		$return['ret_code'] = true;
		$return['message'] = "수정이 완료되었습니다.";
		echo json_encode($return);
		exit;
	}else{
		sql_trans_rollback();
		$return['ret_code'] = false;
		$return['message'] = "업데이트에 실패하였습니다.[동일 정보 업데이트]";
		echo json_encode($return);
		exit;
	}
}else{
	sql_trans_rollback();
	$return['ret_code'] = false;
	$return['message'] = "업데이트에 실패하였습니다.[".$wr_id."]";
	echo json_encode($return);
	exit;
}
?>