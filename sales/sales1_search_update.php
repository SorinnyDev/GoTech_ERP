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
	$item = sql_fetch("select * from g5_write_product where wr_id='".$excel['wr_product_id']."' AND wr_delYn = 'N' ");
	if(!$item['wr_id']){
		$suc2++;
		continue;
	}
	
	$chk = sql_fetch("select * from g5_sales0_list where wr_chk = 1 and wr_order_num = '{$excel['wr_order_num']}' and wr_code = '{$excel['wr_code']}' and wr_set_sku = ''");
	if($chk){
		$suc2++;
		continue;
	}
	
	# 창고 코드
	$wr_warehouse = "";

	if($act == "발주생성") {

    $ed_date = date("Y-m-d");
    $st_date = date("Y-m-d", strtotime("-3 months"));
    $product_id = $item['wr_id'];

    # 안전재고 수량
    $sql = "
      SELECT
      SUM(G0.wr_ea) AS total_sales_ea
      FROM g5_sales3_list G0
      LEFT JOIN g5_write_product WP ON WP.wr_id = G0.wr_product_id
      WHERE wr_release_use = '1'
      AND G0.wr_date4 BETWEEN '$st_date' AND '$ed_date' 
      AND G0.wr_product_id = '{$product_id}'
      GROUP BY
      G0.wr_product_id    
    ";

    $safe_result = sql_fetch($sql);
    $safe_ea = round(($safe_result['total_sales_ea'] / 3) * 2);

		$sql = "insert into g5_sales1_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		wr_product_id = '{$excel['wr_product_id']}',
		wr_product_nm = '".addslashes($excel['wr_product_nm'])."',
		wr_domain = '{$excel['wr_domain']}',
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
		wr_safe_ea = '{$safe_ea}',
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
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
		sql_query($sql, true);
		
		
	} else if($act == "한국창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		sales1_id = '0',
		wr_product_id = '{$excel['wr_product_id']}',
		wr_product_nm = '".addslashes($excel['wr_product_nm'])."',
		wr_domain = '{$excel['wr_domain']}',
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
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '".addslashes($excel['wr_name2'])."',
		wr_etc = '".addslashes($excel['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_warehouse = '1000',
		wr_direct_use = 1
		
		";
		sql_query($sql, true);
		$sales3_id = sql_insert_id();
		$wr_warehouse = "1000";

		# 출고 차감
		$sql = "UPDATE g5_write_product SET wr_32 = wr_32 - {$excel['wr_ea']} WHERE wr_id = '{$excel['wr_product_id']}'";
		sql_query($sql);
		
		
	} else if($act == "미국창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		sales1_id = '0',
		wr_product_id = '{$excel['wr_product_id']}',
		wr_product_nm = '".addslashes($excel['wr_product_nm'])."',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_ori_order_num = '{$excel['wr_ori_order_num']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '".addslashes($excel['wr_mb_name'])."',
		wr_zip = '".addslashes($excel['wr_zip'])."',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
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
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '".addslashes($excel['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_warehouse = '3000',
		wr_direct_use = 1
		
		";

        if(sql_query($sql)){
			$sales3_id = sql_insert_id();
            //sql_query("update g5_write_product set wr_38 = 'N', wr_36 = wr_36-{$excel['wr_ea']} where (wr_1 = '{$excel['wr_code']}' or wr_27 = '{$excel['wr_code']}' or wr_28 = '{$excel['wr_code']}' or wr_29 = '{$excel['wr_code']}' or wr_30 = '{$excel['wr_code']}' or wr_31 = '{$excel['wr_code']}') limit 1");
			sql_query("update g5_write_product set wr_38 = 'N', wr_36 = wr_36-{$excel['wr_ea']} where wr_id='".$excel['wr_product_id']."' limit 1");
        }

		$wr_warehouse = "3000";

	} else if($act == "FBA창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		sales1_id = '0',
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
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
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
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '".addslashes($excel['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_warehouse = '4000',
		wr_direct_use = 1
		
		";
		sql_query($sql);
		$sales3_id = sql_insert_id();
		$wr_warehouse = "4000";

		$sql = "UPDATE g5_write_product SET wr_42 = wr_42 - {$excel['wr_ea']} WHERE wr_id='{$excel['wr_product_id']}'";
		sql_query($sql);

	} else if($act == "W-FBA창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		sales1_id = '0',
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
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
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
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '".addslashes($excel['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_warehouse = '5000',
		wr_direct_use = 1
		
		";
		$rs = sql_query($sql);
		$sales3_id = sql_insert_id();
		$wr_warehouse = "5000";

		$sql = "UPDATE g5_write_product SET wr_43 = wr_43 - {$excel['wr_ea']} WHERE wr_id='{$excel['wr_product_id']}'";
		sql_query($sql);

	} else if($act == "U-FBA창고 출고") {
		
		$sql = "insert into g5_sales2_list set 
		mb_id = '{$excel['mb_id']}',
		wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['seq']}',
		sales1_id = '0',
		wr_product_id = '{$excel['wr_product_id']}',
		wr_product_nm = '".addslashes($excel['wr_product_nm'])."',
		wr_domain = '{$excel['wr_domain']}',
		wr_date = '{$excel['wr_date']}',
		wr_ori_order_num = '{$excel['wr_ori_order_num']}',
		wr_order_num = '{$excel['wr_order_num']}',
		wr_mb_id = '{$excel['wr_mb_id']}',
		wr_mb_name = '".addslashes($excel['wr_mb_name'])."',
		wr_zip = '".addslashes($excel['wr_zip'])."',
		wr_addr1 = '".addslashes($excel['wr_addr1'])."',
		wr_addr2 = '".addslashes($excel['wr_addr2'])."',
		wr_city = '{$excel['wr_city']}',
		wr_ju = '{$excel['wr_ju']}',
		wr_country = '{$excel['wr_country']}',
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
		wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$excel['wr_email']}',
		wr_servicetype = '{$excel['wr_servicetype']}',
		wr_packaging = '{$excel['wr_packaging']}',
		wr_country_code = '{$excel['wr_country_code']}',
		wr_name2 = '{$excel['wr_name2']}',
		wr_etc = '".addslashes($excel['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_set_sku = '".addslashes($excel['wr_set_sku'])."',
		wr_warehouse = '6000',
		wr_direct_use = 1
		";


		$rs = sql_query($sql);
		$sales3_id = sql_insert_id();
		$wr_warehouse = "6000";

		$sql = "UPDATE g5_write_product SET wr_44 = wr_44 - {$excel['wr_ea']} WHERE wr_id='{$excel['wr_product_id']}'";
		sql_query($sql);
	}

	# 발주 생성이 아닌 출고의 경우 g5_rack_stock(랙별 재고 로그) 기록
	if($act != "발주생성"){
		
		# 랙번호 조회
		$rack = sql_fetch("SELECT b.seq, total_stock
		FROM g5_rack b
		LEFT JOIN (
			SELECT wr_rack, SUM(wr_stock) AS total_stock
			FROM g5_rack_stock
			WHERE wr_warehouse = '{$wr_warehouse}'
			AND wr_product_id = '{$excel['wr_product_id']}'
			GROUP BY wr_rack
		) AS a ON a.wr_rack = b.seq
		WHERE a.wr_rack IS NOT NULL 
		AND a.total_stock > 0
		AND b.gc_name != '임시창고'
		ORDER BY b.seq
		LIMIT 1");

		//재고를 뺼 랙이 없는 경우 재고없음으로 처리 24.05.27
		if(!$rack['seq']) {
			$add_seq3[] .= $row['seq'];
			$sql = "DELETE FROM g5_sales2_list WHERE seq='".$sales3_id."'";
			sql_query($sql);

			# 재고 복원
			$field = $storage_arr[$wr_warehouse]['field'];

			$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$excel['wr_ea']} WHERE wr_id='{$excel['wr_product_id']}'";
			sql_query($sql);

			$suc2++;
			continue;
		}

		# 출고
		$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$wr_warehouse}', wr_rack = '{$rack['seq']}', wr_stock = '-{$excel['wr_ea']}', wr_product_id = '{$excel['wr_product_id']}', wr_sales3_id='{$sales3_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
		sql_query($sql);

		$sql = "UPDATE g5_sales2_list SET wr_rack = '{$rack['seq']}' WHERE seq = '{$sales3_id}'";
		sql_query($sql);
	}
	
	sql_query("update g5_sales0_list set wr_chk = 1 where seq = '{$excel['seq']}'");
	$suc++;
}
	$msg = "성공 : {$suc}건 / 실패 : {$suc2}건 {$act}이(가) 완료되었습니다.\\n리스트에서 새로고침해주세요.";
	opener_reload();
	alert($msg);
?>