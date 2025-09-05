<?php
include_once('./_common.php');

if ($is_guest)
    alert('로그인 후 이용하세요.');

$count_chk_wr_id = (isset($_POST['chk_seq']) && is_array($_POST['chk_seq'])) ? count($_POST['chk_seq']) : 0;

if(!$count_chk_wr_id) alert('최소 1개이상 선택하세요.');
$suc1 = 0;
$suc2 = 0;

for ($i=0; $i<$count_chk_wr_id; $i++) {
	
	
	$wr_id_val = isset($_POST['chk_seq'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_seq'][$i]) : 0;
	
	$excel = sql_fetch("select * from g5_sales0_list where seq = '{$wr_id_val}'");

	$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($excel['wr_code'])."' or wr_27 = '".addslashes($excel['wr_code'])."' or wr_28 = '".addslashes($excel['wr_code'])."' or wr_29 = '".addslashes($excel['wr_code'])."' or wr_30 = '".addslashes($excel['wr_code'])."' or wr_31 = '".addslashes($excel['wr_code'])."') ");
	
	/*if($item['wr_32'] >= $excel['wr_ea']) {
		//재고가 있을경우 바로 입고등록으로
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '{$excel['wr_addr1']}',
		wr_addr2 = '{$excel['wr_addr2']}',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '{$excel['wr_tel']}',
		wr_code = '{$excel['wr_code']}',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_order_num2 = '{$excel['wr_order_num2']}',
		wr_orderer = '{$excel['wr_orderer']}',
		wr_order_ea = '{$excel['wr_order_ea']}',
		wr_order_price = '{$excel['wr_order_price']}',
		wr_order_total = '{$excel['wr_order_total']}',
		wr_order_traking = '{$excel['wr_order_traking']}',
		wr_order_etc = '{$excel['wr_order_etc']}',
		wr_warehouse = '{$excel['wr_warehouse']}',
		wr_rack = '{$excel['wr_rack']}',
		wr_warehouse_etc = '{$excel['wr_warehouse_etc']}',
		wr_direct_use = 1
		";
		sql_query($sql, true);
		
		//sql_query("update g5_write_product set wr_32 = wr_32 - {$excel['wr_ea']} where (wr_1 = '{$excel['wr_code']}' or wr_27 = '{$excel['wr_code']}' or wr_28 = '{$excel['wr_code']}' or wr_29 = '{$excel['wr_code']}' or wr_30 = '{$excel['wr_code']}' or wr_31 = '{$excel['wr_code']}') limit 1");
		
		
		$suc1++;
	} else {
		//재고가 없을경우 발주등록
		
		$sql = "insert into g5_sales1_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '{$excel['wr_addr1']}',
		wr_addr2 = '{$excel['wr_addr2']}',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '{$excel['wr_tel']}',
		wr_code = '{$excel['wr_code']}',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
		sql_query($sql);
		
		$suc2++;
	}*/
	
	$chk = sql_fetch("select * from g5_sales0_list where wr_chk = 1 and wr_order_num = '{$excel['wr_order_num']}'");
	
	if($chk){
		$suc2++;
		continue;
	}
	
	if($act == "발주생성") {
		$sql = "insert into g5_sales1_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '".addslashes($excel['wr_tel'])."',
		wr_code = '".addslashes($excel['wr_code'])."',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
		sql_query($sql, true);
		
		
	} else if($act == "한국창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '".addslashes($excel['wr_tel'])."',
		wr_code = '".addslashes($excel['wr_code'])."',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_warehouse = '1000',
		wr_direct_use = 1
		
		";
		sql_query($sql);
		
		//sql_query("update g5_write_product set wr_32 = wr_32 - {$excel['wr_ea']} where (wr_1 = '{$excel['wr_code']}' or wr_27 = '{$excel['wr_code']}' or wr_28 = '{$excel['wr_code']}' or wr_29 = '{$excel['wr_code']}' or wr_30 = '{$excel['wr_code']}' or wr_31 = '{$excel['wr_code']}') limit 1");
		
	} else if($act == "미국창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '".addslashes($excel['wr_tel'])."',
		wr_code = '".addslashes($excel['wr_code'])."',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_warehouse = '3000',
		wr_direct_use = 1
		
		";
		sql_query($sql);

        // 해당 테이블에 접근할 방법 구색해야함. (12-22)
        //sql_query("update g5_write_product set wr_38 = 'N' where wr_subject = '{$excel['wr_order_num']}' ");
		
		sql_query("update g5_write_product set wr_38 = 'N' where (wr_1 = '{$excel['wr_code']}' or wr_27 = '{$excel['wr_code']}' or wr_28 = '{$excel['wr_code']}' or wr_29 = '{$excel['wr_code']}' or wr_30 = '{$excel['wr_code']}' or wr_31 = '{$excel['wr_code']}') limit 1");
		
	} else if($act == "FBA창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '{$excel['wr_mb_name']}',
		wr_zip = '{$excel['wr_zip']}',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
		wr_tel = '".addslashes($excel['wr_tel'])."',
		wr_code = '".addslashes($excel['wr_code'])."',
		wr_ea = '{$excel['wr_ea']}',
		wr_box = '{$excel['wr_box']}',
		wr_danga = '{$excel['wr_danga']}',
		wr_singo = '{$excel['wr_singo']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$excel['wr_weight1']}',
		wr_weight2 = '{$excel['wr_weight2']}',
		wr_weight_dan = '{$excel['wr_weight_dan']}',
		wr_hscode = '{$excel['wr_hscode']}',
		wr_make_country = '{$excel['wr_make_country']}',
		wr_delivery = '{$excel['wr_delivery']}',
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '{$excel['wr_etc']}',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_warehouse = '4000',
		wr_direct_use = 1
		
		";
		sql_query($sql);

        // 해당 테이블에 접근할 방법 구색해야함. (12-22)
        //sql_query("update g5_write_product set wr_38 = 'N' where wr_subject = '{$excel['wr_order_num']}' ");
		
	}
	
	sql_query("update g5_sales0_list set wr_chk = 1 where seq = '{$excel['seq']}'");
	$suc++;
}
	$msg = "성공 : {$suc}건 / 실패 : {$suc2}건 {$act}이(가) 완료되었습니다.\\n리스트에서 새로고침해주세요.";

	alert_close($msg);
?>