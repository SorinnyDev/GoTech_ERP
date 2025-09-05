<?php
include_once('./_common.php');

if ($is_guest)
    alert('로그인 후 이용하세요.');

$count_chk_wr_id = (isset($_POST['chk_seq']) && is_array($_POST['chk_seq'])) ? count($_POST['chk_seq']) : 0;

if(!$count_chk_wr_id) alert('최소 1개이상 선택하세요.');
$suc1 = 0;
$suc2 = 0;
$fail = 0;
for ($i=0; $i<$count_chk_wr_id; $i++) {
	
	
	$wr_id_val = isset($_POST['chk_seq'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_seq'][$i]) : 0;

	$excel = sql_fetch("select * from g5_sales1_list where seq = '{$wr_id_val}'");

	#$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($excel['wr_code'])."' or wr_27 = '".addslashes($excel['wr_code'])."' or wr_28 = '".addslashes($excel['wr_code'])."' or wr_29 = '".addslashes($excel['wr_code'])."' or wr_30 = '".addslashes($excel['wr_code'])."' or wr_31 = '".addslashes($excel['wr_code'])."') AND wr_delYn = 'N' ");
	$item = sql_fetch("select * from g5_write_product where wr_id='".$excel['wr_product_id']."' AND wr_delYn = 'N' ");
	if(!$item['wr_id']){
		$fail++;
		continue;
	}

	$chk = sql_fetch("select * from g5_sales1_list where wr_chk = 1 and wr_order_num = '{$excel['wr_order_num']}' and wr_set_sku = ''"); 
	
	if($chk){
		continue;
	}
	
	$sql = "insert into g5_sales2_list set 
	mb_id = '{$excel['mb_id']}',
	wr_id = '{$wr_id_val}',
	sales0_id = '{$excel['sales0_id']}',
	sales1_id = '{$excel['seq']}',
	wr_domain = '{$excel['wr_domain']}',
	wr_product_id = '{$excel['wr_product_id']}',
	wr_product_nm = '".addslashes($excel['wr_product_nm'])."',
	wr_date = '{$excel['wr_date']}',
	wr_ori_order_num = '{$excel['wr_ori_order_num']}',
	wr_order_num = '{$excel['wr_order_num']}',
	wr_mb_id = '{$excel['wr_mb_id']}',
	wr_mb_name = '".addslashes($excel['wr_mb_name'])."',
	wr_zip = '".addslashes($excel['wr_zip'])."',
	wr_addr1 = '".addslashes($excel['wr_addr1'])."',
	wr_addr2 = '".addslashes($excel['wr_addr2'])."',
	wr_city = '".addslashes($excel['wr_city'])."',
	wr_ju = '".addslashes($excel['wr_ju'])."',
	wr_country = '".addslashes($excel['wr_country'])."',
	wr_tel = '".addslashes($excel['wr_tel'])."',
	wr_deli_nm = '".addslashes($excel['wr_deli_nm'])."',
	wr_deli_zip = '".addslashes($excel['wr_deli_zip'])."',
	wr_deli_addr1 = '".addslashes($excel['wr_deli_addr1'])."',
	wr_deli_addr2 = '".addslashes($excel['wr_deli_addr2'])."',
	wr_deli_city = '".addslashes($excel['wr_deli_city'])."',
	wr_deli_ju = '".addslashes($excel['wr_deli_ju'])."',
	wr_deli_country = '".addslashes($excel['wr_deli_country'])."',
	wr_deli_tel = '".addslashes($excel['wr_deli_tel'])."',
	wr_code = '".addslashes($excel['wr_code'])."',
	wr_ea = '{$excel['wr_ea']}',
	wr_box = '{$excel['wr_box']}',
	wr_paymethod = '".addslashes($excel['wr_paymethod'])."',
	wr_danga = '{$excel['wr_danga']}',
	wr_singo = '{$excel['wr_singo']}',
	wr_tax = '{$excel['wr_tax']}',
	wr_shipping_price = '{$excel['wr_shipping_price']}',
	wr_fee1 = '{$excel['wr_fee1']}',
	wr_fee2 = '{$excel['wr_fee2']}',
	wr_taxType = '{$excel['wr_taxType']}',
	wr_exchange_rate = '{$excel['wr_exchange_rate']}',
	wr_sales_fee_type = '{$excel['wr_sales_fee_type']}',
	wr_sales_fee = '{$excel['wr_sales_fee']}',
	wr_currency = '{$excel['wr_currency']}',
	wr_weight1 = '{$excel['wr_weight1']}',
	wr_weight2 = '{$excel['wr_weight2']}',
	wr_weight3 = '{$excel['wr_weight3']}',
	wr_weight_dan = '{$excel['wr_weight_dan']}',
	wr_hscode = '{$excel['wr_hscode']}',
	wr_make_country = '{$excel['wr_make_country']}',
	wr_delivery = '{$excel['wr_delivery']}',
	wr_delivery_fee = '{$excel['wr_delivery_fee']}',
	wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
	wr_delivery_oil = '{$excel['wr_delivery_oil']}',
	wr_email = '{$excel['wr_email']}',
	wr_servicetype = '{$excel['wr_servicetype']}',
	wr_packaging = '{$excel['wr_packaging']}',
	wr_country_code = '{$excel['wr_country_code']}',
	wr_name2 = '".addslashes($excel['wr_name2'])."',
	wr_etc = '".addslashes($excel['wr_etc'])."',
	wr_date2 = '{$excel['wr_date2']}',
	wr_date3 = '{$_POST['wr_date3']}',
	wr_order_num2 = '{$excel['wr_order_num2']}',
	wr_orderer = '{$excel['wr_orderer']}',
	wr_order_ea = '{$excel['wr_order_ea']}',
	wr_order_price = '{$excel['wr_order_price']}',
	wr_order_fee = '{$excel['wr_order_fee']}',
	wr_order_total = '{$excel['wr_order_total']}',
	wr_misu = '{$excel['wr_order_total']}',
	wr_order_traking = '{$excel['wr_order_traking']}',
	wr_order_etc = '{$excel['wr_order_etc']}',
	wr_etc_use = '{$excel['wr_etc_chk']}',
	wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
	wr_warehouse = '{$_POST['warehouse']}',
	wr_ibgo_ea = '{$excel['wr_order_ea']}',
	wr_chul_ea = '{$excel['wr_order_ea']}'
	
	";
	sql_query($sql, true);
	$sales2_id = sql_insert_id();
	
	sql_query("update g5_sales1_list set wr_chk = 1 where seq = '{$excel['seq']}'");
		
	//24.05.03 변경
	//발주건에서 주문수량만큼 재고차감하고 바로 출고등록>입고자료가져오기로 보냄(위에서 처리)
	//입고 가져오기에 들어왔으니 재고 증감 
	if($warehouse == 1000){
		$stock_field = 'wr_32';
		$stock_real_field = 'wr_32_real';
	}else if($warehouse == 3000){
		$stock_field = 'wr_36';
		$stock_real_field = 'wr_36_real';
	}else if($warehouse == 4000){
		$stock_field = 'wr_42';
		$stock_real_field = 'wr_42_real';
	}else if($warehouse == 5000){
		$stock_field = 'wr_43';
		$stock_real_field = 'wr_43_real';
	}else if($warehouse == 6000){
		$stock_field = 'wr_44';
		$stock_real_field = 'wr_44_real';
	}else if($warehouse == 7000){
		$stock_field = 'wr_40';
		$stock_real_field = 'wr_40_real';
	}else if($warehouse == 8000){
		$stock_field = 'wr_41';
		$stock_real_field = 'wr_41_real';
	}else if($warehouse == 9000){
		$stock_field = 'wr_37';
		$stock_real_field = 'wr_37_real';
	}

	//24.06.26 기타주문건은 처리되면 안됨 아래에서 임시창고로 바로 보내져야함.
	if($excel['wr_etc_chk'] != 1) {
		//입고랙 찾기
		$rack = sql_fetch("select seq from g5_rack where gc_warehouse = '{$warehouse}'");
		
		//주문수량만큼 랙에 입고
		$sql2 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack['seq']}', wr_stock = '{$excel['wr_ea']}', wr_product_id = '{$item['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_sales3_id='{$sales2_id}', wr_move_log = '입고처리'";
		sql_query($sql2);
		
		//24.06.26 수정요청으로 인해 입고처리 후 재고바로차감 
		$sql3 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack['seq']}', wr_stock = '-{$excel['wr_ea']}', wr_product_id = '{$item['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_sales3_id='{$sales2_id}', wr_move_log = '입고처리 후 재고차감'";
		sql_query($sql3);
		
		//입고처리 된 랙번호 기록
		sql_query("update g5_sales2_list set wr_rack = '{$rack['seq']}' where seq = '{$sales2_id}' LIMIT 1");
	}
	
	//창고 재고 증감 , 위 24.06.26 업데이트로 수량변동 없어지므로 업데이트안함. 
	#sql_query("update g5_write_product set {$stock_real_field} = {$stock_real_field} + {$excel['wr_ea']} where wr_id = '{$item['wr_id']}' limit 1");
	
	
	//남은 수량이 있다면 임시창고로 보냄(아래)
	//발주수량 - 주문수량
	$stock = (int)$excel['wr_order_ea'] - (int)$excel['wr_ea']; 
	
	if($stock > 0 && $excel['wr_etc_chk'] == 0) {
		//stock 재고가 남았을때 임시창고로 
		sql_query("insert into g5_temp_warehouse set  
		sales1_id = '{$excel['seq']}',
		sales2_id = '{$sales2_id}',
		wr_product_id = '{$item['wr_id']}',
		wr_stock = {$stock},
		wr_stock2 = {$excel['wr_ea']},
		wr_mb_id = '{$member['mb_id']}',
		wr_datetime = '".G5_TIME_YMDHIS."'
		");
		
		//임시창고 바로입고 231120 
		sql_query("update g5_write_product set wr_37 = wr_37 + {$stock}, wr_37_real = wr_37_real + {$excel['wr_order_ea']} where wr_id = '{$item['wr_id']}' ");
	}else if($stock == 0 && $excel['wr_etc_chk'] == 0){
		sql_query("insert into g5_temp_warehouse set  
		sales1_id = '{$excel['seq']}',
		sales2_id = '{$sales2_id}',
		wr_product_id = '{$item['wr_id']}',
		wr_stock = {$stock},
		wr_stock2 = {$excel['wr_ea']},
		wr_mb_id = '{$member['mb_id']}',
		wr_datetime = '".G5_TIME_YMDHIS."'
		");

		sql_query("update g5_write_product set wr_37_real = wr_37_real + {$excel['wr_order_ea']} where wr_id = '{$item['wr_id']}' ");
	}
	
	//기타주문건은 실 주문수량이 없기때문에 바로 임시창고로
	if($excel['wr_etc_chk'] == 1) {
		sql_query("insert into g5_temp_warehouse set  
		sales1_id = '{$excel['seq']}',
		sales2_id = '{$sales2_id}',
		wr_product_id = '{$item['wr_id']}',
		wr_stock = {$excel['wr_order_ea']},
		wr_stock2 = {$excel['wr_ea']},
		wr_mb_id = '{$member['mb_id']}',
		wr_datetime = '".G5_TIME_YMDHIS."'
		");
		
		//임시창고 바로입고 231120 
		sql_query("update g5_write_product set wr_37 = wr_37 + {$excel['wr_order_ea']}, wr_37_real = wr_37_real + {$excel['wr_order_ea']} where wr_id = '{$item['wr_id']}' ");
		$suc2++;
	}
	
  $sql = "select `value` from g5_sales_metadata where entity_type = 'g5_sales1_list' and entity_id = '{$excel['seq']}' and `key` = 'code_card'";
  $metadata = sql_fetch($sql);

  if ($metadata['value'] && $metadata['value'] != '1461') {
    $sql = "update g5_sales2_list set wr_misu = 0, wr_warehouse_price = '{$excel['wr_order_total']}' where seq = '{$sales2_id}' LIMIT 1";
    sql_query($sql);
  }

	
}
	$msg = "입고등록이 완료되었습니다(실패 : ".$fail.").\\n리스트에서 새로고침해주세요.";
	opener_reload();
	alert($msg);
?>