<?php
include_once('./_common.php');

if ($is_guest) die('n');

$upColumn = "wr_date2 = '".$wr_date2."'
			,wr_order_num2 = '".addslashes($wr_order_num2)."'
			,wr_orderer = '".addslashes($wr_orderer)."'
			,wr_order_traking = '".addslashes($wr_order_traking)."'
			,wr_order_ea = '".addslashes($wr_order_ea)."'
			,wr_order_price = '".$wr_order_price."'
			,wr_order_fee = '".$wr_order_fee."'
			,wr_order_total = '".$wr_order_total."'
			,wr_order_etc = '".addslashes($wr_order_etc)."'
			,wr_delivery = '".addslashes($wr_delivery)."'
			,wr_delivery_fee2 = '".$wr_delivery_fee2."'
			,wr_delivery_fee = '".$wr_delivery_fee."'
			,wr_delivery_oil = '".$wr_delivery_oil."'
			,wr_singo = '".$wr_singo."'
			,wr_danga = '".$wr_danga."'
			,wr_fee1 = '".$wr_fee1."'
			,wr_fee2 = '".$wr_fee2."'
			,wr_taxType = '".$wr_taxType."'
			,mb_id = '".addslashes($wmb_id)."'";

# 기타주문건의 경우 발주 수량 변경시 주문 수량도 함께 변경
if($wr_etc_chk == "1"){
	$upColumn .= "			,wr_ea='".$wr_order_ea."' ";
}

/*
$sql = "update g5_sales1_list set 
	wr_order_num2 = '{$wr_order_num2}',
	wr_orderer = '{$wr_orderer}',
	wr_order_ea = '{$wr_order_ea}',
	wr_order_price = '{$wr_order_price}',
	wr_order_total = '{$wr_order_total}',
	wr_order_fee = '{$wr_order_fee}',
	wr_order_traking = '{$wr_order_traking}',
	wr_order_etc = '{$wr_order_etc}'
	
	where seq = '{$seq}'
*/
$sql = "UPDATE g5_sales1_list SET ";
$sql .= $upColumn;
$sql .= "WHERE seq = '".$seq."'";
if(sql_query($sql, true)) {
	
	# 주문번호 가져오기
	$sql = "SELECT * FROM g5_sales1_list WHERE seq = '".$seq."'";
	$data = sql_fetch($sql);

	# 세트 상품의 경우 해당 원주문번호,같은 세트 상품 동일하게 단가,신고가격,수수료1,수수료2 수정
	if(isDefined($data['wr_set_sku']) == true){
		$sql = "UPDATE g5_sales1_list SET \n";
		$sql .= "	wr_danga = '".$wr_danga."' \n";
		$sql .= "	,wr_singo = '".$wr_singo."' \n";
		$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
		$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
		$sql .= "WHERE wr_ori_order_num = '".$data['wr_ori_order_num']."' AND wr_set_sku = '".$data['wr_set_sku']."'";
		sql_query($sql);
	}

	# 하위 매출정보 담당자 업데이트
	$sql = "UPDATE g5_sales0_list SET mb_id = '".addslashes($wmb_id)."' WHERE wr_order_num = '".$data['wr_order_num']."'";
	sql_query($sql);

	# 세트 상품의 경우 해당 원주문번호,같은 세트 상품 동일하게 단가,신고가격,수수료1,수수료2 수정
	if(isDefined($data['wr_set_sku']) == true){
		$sql = "UPDATE g5_sales0_list SET \n";
		$sql .= "	wr_danga = '".$wr_danga."' \n";
		$sql .= "	,wr_singo = '".$wr_singo."' \n";
		$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
		$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
		$sql .= "WHERE wr_ori_order_num = '".$data['wr_ori_order_num']."' AND wr_set_sku = '".$data['wr_set_sku']."'";
		sql_query($sql);
	}
	

	# 입고 정보 있을 경우 업데이트
	$sql = "UPDATE g5_sales2_list SET ";
	$sql .= $upColumn;
	$sql .= "WHERE wr_order_num = '".$data['wr_order_num']."'";
	sql_query($sql);

	# 세트 상품의 경우 해당 원주문번호,같은 세트 상품 동일하게 단가,신고가격,수수료1,수수료2 수정
	if(isDefined($data['wr_set_sku']) == true){
		$sql = "UPDATE g5_sales2_list SET \n";
		$sql .= "	wr_danga = '".$wr_danga."' \n";
		$sql .= "	,wr_singo = '".$wr_singo."' \n";
		$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
		$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
		$sql .= "WHERE wr_ori_order_num = '".$data['wr_ori_order_num']."' AND wr_set_sku = '".$data['wr_set_sku']."'";
		sql_query($sql);
	}

	# 출고 정보 있을 경우 업데이트
	$sql = "UPDATE g5_sales3_list SET ";
	$sql .= $upColumn;
	$sql .= "WHERE wr_order_num = '".$data['wr_order_num']."'";
	sql_query($sql);

	# 세트 상품의 경우 해당 원주문번호,같은 세트 상품 동일하게 단가,신고가격,수수료1,수수료2 수정
	if(isDefined($data['wr_set_sku']) == true){
		$sql = "UPDATE g5_sales3_list SET \n";
		$sql .= "	wr_danga = '".$wr_danga."' \n";
		$sql .= "	,wr_singo = '".$wr_singo."' \n";
		$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
		$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
		$sql .= "WHERE wr_ori_order_num = '".$data['wr_ori_order_num']."' AND wr_set_sku = '".$data['wr_set_sku']."'";
		sql_query($sql);
	}

	# 메타데이터 결제카드 수정
	if (isDefined($metadata_code_card)) {
		$sql = "SELECT * FROM g5_sales_metadata WHERE entity_type = 'g5_sales1_list' AND entity_id = '{$seq}' AND `key` = 'code_card'";
		$card = sql_fetch($sql);

		if (!empty($card['id'])) {
			$sql = "UPDATE g5_sales_metadata SET `value` = '".$metadata_code_card."' WHERE id = {$card['id']}";
		} else {
			$sql = "INSERT INTO g5_sales_metadata SET entity_type = 'g5_sales1_list', entity_id = '{$seq}', `key` = 'code_card', `value` = '".$metadata_code_card."'";
		}
		sql_query($sql);

		# 카드번호 지정 시 발주금액과 동일한 지급금 입력
		# 현금이 아닐 경우
		if ($metadata_code_card && $metadata_code_card != '1461') {
			$sql = "SELECT * FROM g5_sales2_list WHERE wr_order_num = '{$data['wr_order_num']}'";
			$sales1_item = sql_fetch($sql);

			if ($sales1_item['seq']) {
				$misu = $sales1_item['wr_misu'] - $data['wr_order_total'];
				$misu = $misu < 0 ? 0 : $misu;

				$sql = "UPDATE g5_sales2_list SET wr_warehouse_price = '{$data['wr_order_total']}', wr_misu = '$misu' WHERE wr_order_num = '{$data['wr_order_num']}'";
				sql_query($sql);
			}

		}
	}

		# 발주등록 데이터 수정 시 상품 마스터의 발주단가 수정
	$wr_product_id = $data['wr_product_id'];
	$sql = "SELECT * FROM g5_write_product WHERE wr_id = '$wr_product_id'";
	$wp = sql_fetch($sql);



	if ($wp['wr_id'] && $wp['wr_22'] != $wr_order_price) {
		# 이전 값 메타데이터 삽입
		$sql = "INSERT INTO g5_product_metadata SET entity_type = 'g5_write_product', entity_id = '$wr_product_id', `key` = 'before_wr_22', `value` = '".$wp['wr_22']."'";
		sql_query($sql);

		# 값 변경
		$sql = "UPDATE g5_write_product SET wr_22 = '$wr_order_price' WHERE wr_id = '$wr_product_id'";
		sql_query($sql);
	}



	/*
	$cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_sales2_list WHERE wr_order_num = '{$wr_order_num}' ")['cnt'];
	if($cnt > 0){
		$sql = " UPDATE g5_sales2_list SET wr_order_ea = '{$wr_order_ea}', wr_order_price = '{$wr_order_price}', wr_order_total = '{$wr_order_total}', wr_order_fee = '{$wr_order_fee}' WHERE wr_order_num = '{$wr_order_num}' ";
		sql_query($sql);
	}    
	*/
	
	die('y');
} else {
	die('n');
}
?>