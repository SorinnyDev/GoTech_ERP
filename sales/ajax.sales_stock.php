<?
include_once("./_common.php");

# 반환 데이터
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

# 변수 선언
$direct = false; // 바로출고 여부

/*******************************************
각 단계별 테이블 조회
입고 및 출고 등록이 된 데이터의 경우 수량 변경 불가
구조상 재고 집계에 오류가 발생할 확률이 높음
*******************************************/
if($mode == "sales0"){
	$sql = "SELECT * FROM g5_sales0_list WHERE seq='".$seq."'";
	$row = sql_fetch($sql);
	
	$sales0_id = $row['seq'];
}else if($mode == "sales1"){
	$sql = "SELECT * FROM g5_sales1_list WHERE seq='".$seq."'";
	$row = sql_fetch($sql);

	$sales0_id = $row['sales0_id'];

}else{
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.";
	echo json_encode($return);
	exit;
}

# 출고등록 정보 조회
$sql = "SELECT * FROM g5_sales3_list WHERE sales0_id='".$sales0_id."'";
$sales3 = sql_fetch($sql);

# 입고등록 정보 조히
$sql = "SELECT * FROM g5_sales2_list WHERE sales0_id='".$sales0_id."'";
$sales2 = sql_fetch($sql);

# 출고완료된 주문인지 체크
# wr_release_use가 1이 아닐 경우 출고등록이 되지 않았거나 입고 등록이 되지 않았을 경우
if($sales3['wr_release_use'] == "1"){
	$return['ret_code'] = false;
	$return['message'] = "출고 완료가 된 주문입니다.";
	$return['wr_ea'] = $row['wr_ea'];
	echo json_encode($return);
	exit;
}else{
	# 바로 출고가 아닌 발주 -> 입고 순서로 왔을 경우 변경이 안된도록 
	# wr_direct_use가 0일 ㅕ경우 발주단계부터 온 거이므로 0이 아닐 경우는 입고 등록이 안되었거나 바로 출고의 경우
	if($sales2['wr_direct_use'] == "0"){
		$return['ret_code'] = false;
		$return['message'] = "입고가 된 상품입니다.";
		$return['wr_ea'] = $row['wr_ea'];
		echo json_encode($return);
		exit;
	}
}

if($sales2['wr_direct_use'] == "1"){
	$field = $storage_arr[$row['wr_warehouse']]['field'];
	$field_real = $storage_arr[$row['wr_warehouse']]['field_real'];

	# 상품에서 재고 수량 불러오기
	$item = sql_fetch("SELECT ".$field." AS product_stock, ".$field_real." AS product_stock_real FROM g5_write_product WHERE wr_id='".$row['wr_product_id']."'");
	if((int)$item['product_stock']+(int)$row['wr_ea'] < $wr_ea){
		$return['ret_code'] = false;
		$return['message'] = "최대 ".((int)$item['product_stock'] + (int)$row['wr_ea'])."개까지 가능합니다.";
		$return['wr_ea'] = $row['wr_ea'];
		echo json_encode($return);
		exit;
	}else{
		echo json_encode($return);
		exit;
	}
}

echo json_encode($return);
exit;
?>