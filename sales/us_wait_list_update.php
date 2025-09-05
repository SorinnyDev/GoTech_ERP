<?php
include_once('./_common.php');
//24.05.28 신규추가;

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

$count = (isset($_POST['seq']) && is_array($_POST['seq'])) ? count($_POST['seq']) : 0;

if(!$count) {
    alert('출고 하실 항목을 하나 이상 선택하세요.');
}
$suc_cnt = 0; //처리완료
$fail_cnt = 0; //재고부족
$fail2_cnt = 0; //중복 

for($i=0; $i<$count; $i++) {
	
    $row = sql_fetch("select * from g5_sales1_list where seq = '{$_POST['seq'][$i]}'");
	
    if($_POST['btn_submit']=="일괄출고"){
		
		$chk = sql_fetch("select * from g5_sales1_list where wr_chk = 1 and wr_order_num = '{$row['wr_order_num']}' and wr_code = '{$row['wr_code']}' and wr_set_sku = ''");
		if($chk){
			$fail2_cnt++;
			continue;
		}
		
		
		//재고가 부족하면 실행안함.
		$item = sql_fetch("select wr_36 from g5_write_product where wr_id = '{$row['wr_product_id']}'");
		
		if($item['wr_36'] < $row['wr_ea']) {
			$fail_cnt++;
			continue;
			
		} else {

			$rack = sql_fetch("select * from g5_rack_stock where 
					wr_warehouse = '{$info['wr_warehouse']}' and
					wr_product_id = '{$info['wr_product_id']}' AND wr_stock < 0 ORDER BY seq DESC");
		
			$sql2 = "insert into g5_sales2_list set 
			mb_id = '{$row['mb_id']}',
			wr_id = '{$row['seq']}',
			sales0_id = '{$row['sales0_id']}',
			sales1_id = '{$row['seq']}',
			wr_domain = '{$row['wr_domain']}',
			wr_date = '{$row['wr_date']}',
			wr_ori_order_num = '{$row['wr_ori_order_num']}',
			wr_order_num = '{$row['wr_order_num']}',
			wr_mb_id = '{$row['wr_mb_id']}',
			wr_mb_name = '".addslashes($row['wr_mb_name'])."',
			wr_zip = '".addslashes($row['wr_zip'])."',
			wr_addr1 = '".addslashes($row['wr_addr1'])."',
			wr_addr2 = '".addslashes($row['wr_addr2'])."',
			wr_city = '".addslashes($row['wr_city'])."',
			wr_ju = '".addslashes($row['wr_ju'])."',
			wr_country = '".addslashes($row['wr_country'])."',
			wr_tel = '".addslashes($row['wr_tel'])."',
			wr_deli_nm = '".addslashes($row['wr_deli_nm'])."',
			wr_deli_zip = '".addslashes($row['wr_deli_zip'])."',
			wr_deli_addr1 = '".addslashes($row['wr_deli_addr1'])."',
			wr_deli_addr2 = '".addslashes($row['wr_deli_addr2'])."',
			wr_deli_city = '".addslashes($row['wr_deli_city'])."',
			wr_deli_ju = '".addslashes($row['wr_deli_ju'])."',
			wr_deli_country = '".addslashes($row['wr_deli_country'])."',
			wr_deli_tel = '".addslashes($row['wr_deli_tel'])."',
			wr_code = '".addslashes($row['wr_code'])."',
			wr_ea = '{$row['wr_ea']}',
			wr_box = '{$row['wr_box']}',
			wr_paymethod = '".addslashes($row['wr_paymethod'])."',
			wr_danga = '{$row['wr_danga']}',
			wr_singo = '{$row['wr_singo']}',
			wr_tax = '{$row['wr_tax']}',
			wr_shipping_price = '{$row['wr_shipping_price']}',
			wr_fee1 = '{$row['wr_fee1']}',
			wr_fee2 = '{$row['wr_fee2']}',
			wr_currency = '{$row['wr_currency']}',
			wr_weight1 = '{$row['wr_weight1']}',
			wr_weight2 = '{$row['wr_weight2']}',
			wr_weight_dan = '{$row['wr_weight_dan']}',
			wr_hscode = '{$row['wr_hscode']}',
			wr_make_country = '{$row['wr_make_country']}',
			wr_delivery = '{$row['wr_delivery']}',
			wr_delivery_fee = '{$row['wr_delivery_fee']}',
			wr_email = '{$row['wr_email']}',
			wr_servicetype = '{$row['wr_servicetype']}',
			wr_packaging = '{$row['wr_packaging']}',
			wr_country_code = '{$row['wr_country_code']}',
			wr_name2 = '".addslashes($row['wr_name2'])."',
			wr_etc = '".addslashes($row['wr_etc'])."',
			wr_date2 = '{$row['wr_date']}',
			wr_date3 = '{$row['wr_date']}',
			wr_rack = '{$row['wr_rack']}',
			wr_warehouse_etc = '{$row['wr_warehouse_etc']}',
			wr_warehouse = '3000',
			wr_direct_use = 0,
			wr_product_id = '{$row['wr_product_id']}',
			wr_set_sku = '".addslashes($row['wr_set_sku'])."',
			wr_datetime = '".G5_TIME_YMDHIS."'
			";
			
			sql_query($sql2, true);
			$sales2_id = sql_insert_id();
			
			# 재고에서 차감
			$sql = "UPDATE g5_write_product SET wr_36 = wr_36 - {$row['wr_ea']} WHERE wr_id = '{$row['wr_product_id']}'";
			sql_query($sql);

			$rack = sql_fetch("SELECT b.seq, total_stock
			FROM g5_rack b
			LEFT JOIN (
				SELECT wr_rack, SUM(wr_stock) AS total_stock
				FROM g5_rack_stock
				WHERE wr_warehouse = '{$row['wr_warehouse']}'
				AND wr_product_id = '{$row['wr_product_id']}'
				GROUP BY wr_rack
			) AS a ON a.wr_rack = b.seq
			WHERE a.wr_rack IS NOT NULL 
			AND a.total_stock > 0
			AND b.gc_name != '임시창고'
			ORDER BY b.seq
			LIMIT 1");

			$upSql = "UPDATE g5_sales2_list SET wr_rack = '{$rack['seq']}' WHERE seq = '{$sales2_id}'";
			sql_query($upSql);

			# 랙재고 차감
			$sql = "insert into g5_rack_stock set wr_warehouse = '{$row['wr_warehouse']}', wr_rack = '{$rack['seq']}', wr_stock = '-{$row['wr_ea']}', wr_product_id = '{$row['wr_product_id']}', wr_sales3_id = '{$row['wr_sales3_id']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '미국출고대기건 출고 처리'";
			sql_query($sql);

			//처리체크
			sql_query("update g5_sales1_list set wr_chk = 1 where seq = '{$row['seq']}'");
			
			$suc_cnt++;

		}
	}
}

$msg = "출고등록 : {$suc_cnt}\\n재고부족 : {$fail_cnt}\\n주문번호중복 : {$fail2_cnt}\\n처리되었습니다.";
alert($msg);