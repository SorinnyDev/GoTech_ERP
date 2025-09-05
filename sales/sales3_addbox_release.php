<?
include_once('./_common.php');

$excel = sql_fetch("select * from g5_sales3_list where seq = '{$seq}'");

//24.05.30 	바로출고건이 아닌경우 재고가있는 랙이 없을 경우 실행중지
//24.06.26 입고생성시 재고차감 되므로 필요없음.
/*if($excel['wr_direct_use'] != 1) {
	$rack = sql_fetch("SELECT b.seq, total_stock
		FROM g5_rack b
		LEFT JOIN (
			SELECT wr_rack, SUM(wr_stock) AS total_stock
			FROM g5_rack_stock
			WHERE wr_warehouse = '{$excel['wr_warehouse']}'
			AND wr_product_id = '{$excel['wr_product_id']}'
			GROUP BY wr_rack
		) AS a ON a.wr_rack = b.seq
		WHERE a.wr_rack IS NOT NULL 
		AND a.total_stock > 0
		AND b.gc_name != '임시창고'
		ORDER BY b.seq
		LIMIT 1");
	
		
	if(!$rack) die('nn');
}*/


$sql = "update g5_sales3_list set 
	wr_rack = '{$excel['wr_rack']}',
	wr_delivery = '{$wr_delivery}',
	wr_delivery_fee = '{$wr_delivery_fee}',
	wr_delivery_fee2 = '{$wr_delivery_fee2}',
	wr_release_use = 1,
	wr_release_date = '".G5_TIME_YMDHIS."',
	wr_release_mbid = '".$member['mb_id']."'
	where seq = '{$seq}'
	";
	if(sql_query($sql, true)) {
			#########################################################
			# 실재고 수량 기록을 위해 실재고 수량 업데이트
			#########################################################
			if($excel['wr_warehouse'] == '9000'){
				$filed_real = 'wr_37_real';
			}else if($excel['wr_warehouse'] == '1000'){
				$filed_real = 'wr_32_real';
			}else if($excel['wr_warehouse'] == '3000'){
				$filed_real = 'wr_36_real';
			}
			if($excel['wr_release_use'] != "1"){
				if($excel['wr_direct_use'] == 1 || $row['wr_warehouse'] == "3000") {
					sql_query("update g5_write_product set {$filed_real} = {$filed_real} - {$excel['wr_ea']} where wr_id = '{$excel['wr_product_id']}' ");
				}else{
					sql_query("update g5_write_product set wr_37_real = wr_37_real - {$excel['wr_ea']} where wr_id = '{$excel['wr_product_id']}' ");
				}
			}
			//24.06.26 입고생성시 재고차감 되므로 필요없음.
			/*if($excel['wr_direct_use'] != 1) {
				if($excel['wr_warehouse'] == '9000')
					$filed = 'wr_37';
				else if($excel['wr_warehouse'] == '1000')
					$filed = 'wr_32';
				else if($excel['wr_warehouse'] == '3000')
					$filed = 'wr_36';
				
				sql_query("update g5_write_product set {$filed} = {$filed} - {$excel['wr_ea']} where wr_id = '{$excel['wr_product_id']}' limit 1");
				
				$sql = "insert into g5_rack_stock set wr_warehouse = '{$excel['wr_warehouse']}', wr_rack = '{$rack['seq']}', wr_stock = '-{$excel['wr_ea']}', wr_product_id = '{$excel['wr_product_id']}', wr_sales3_id = '{$seq}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
				sql_query($sql);
			}*/
			
		die('y');
	} else {
		die('n');
	}
?>