<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

if(!$_POST['wr_id']) alert('잘못 된 접근입니다.');

// if (!$_POST['ms_rack2']) die('선택된 랙이 없습니다.');

if($mode == "move1_kor") {
	$sql = "insert into g5_rack_stock set wr_warehouse = '1000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', 
					wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[한국창고(1000)] 미분류 재고가 랙으로 이동되었습니다.');	
} else if($mode == "move1_usa") {
	$sql = "insert into g5_rack_stock set wr_warehouse = '3000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', 
					wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[미국창고(3000)] 미분류 재고가 랙으로 이동되었습니다.');
} else if($mode == "move2") {
	$ms_stock = trim($_POST['ms_stock']);
	$ms_stock = (int)$ms_stock;
	
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse}', wr_rack = '{$ms_rack}', wr_stock = '-{$ms_stock}', 
					wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	$sql2 = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse2}', wr_rack = '{$ms_rack2}', wr_stock = '{$ms_stock}',
					 wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql2);

	$minus_field = $storage_arr[$ms_warehouse]['field'];
	$plus_field = $storage_arr[$ms_warehouse2]['field'];

	$minus_field_real = $storage_arr[$ms_warehouse]['field_real'];
	$plus_field_real = $storage_arr[$ms_warehouse2]['field_real'];
	
	sql_query("update g5_write_product set {$minus_field} = {$minus_field} - {$ms_stock},
						{$plus_field} = {$plus_field} + {$ms_stock},{$minus_field_real} = {$minus_field_real} - {$ms_stock},
						{$plus_field_real} = {$plus_field_real} + {$ms_stock} where wr_id = '{$wr_id}'");
	
	
	alert('재고가 선택하신 창고와 랙으로 이동되었습니다.');

} else if($mode == "indi") {	
	sql_trans_start();
	
	$db_flag = true;
	
	$sql = "SELECT IFNULL(SUM(A.wr_ea),0) AS sales_stock FROM g5_sales2_list A \n";
	$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num \n";
	$sql .= "WHERE A.wr_etc_use='0' AND A.wr_direct_use='1' AND A.wr_rack='".$seq."' AND A.wr_product_id='".$wr_id."' AND A.wr_warehouse='".$warehouse."' AND IFNULL(B.wr_release_use,'0') = '0'";
	$salesData = sql_fetch($sql);
	$sales_ea = $salesData['sales_stock'];

	$total_stock = $ori_qty + $sales_ea;
	$calc_stock = $qty - $total_stock;
	if($calc_stock != 0){
		$sql = "INSERT INTO g5_rack_stock SET \n";
		$sql .= "	wr_warehouse='".$warehouse."' \n";
		$sql .= "	,wr_rack = '".$seq."' \n";
		$sql .= "	,wr_stock = '".$calc_stock."' \n";
		$sql .= "	,wr_product_id = '".$wr_id."' \n";
		$sql .= "	,wr_mb_id = '".$member['mb_id']."' \n";
		$sql .= "	,wr_datetime = NOW() \n";
		$sql .= "	,wr_move_log = '직접 변경'";
		$rs = sql_query($sql);

		if($rs){
			$sql = "SELECT IFNULL(SUM(wr_stock),0) AS rack_stock FROM g5_rack_stock WHERE wr_product_id='".$wr_id."' AND wr_warehouse='".$warehouse."'";
			$rack_stock_data = sql_fetch($sql);
			$rack_stock = $rack_stock_data['rack_stock'];

			$field = $storage_arr[$warehouse]['field'];
			$field_real = $storage_arr[$warehouse]['field_real'];

			$sql = "SELECT IFNULL(SUM(A.wr_ea),0) AS total_sales_ea FROM g5_sales2_list A \n";
			$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num \n";
			$sql .= "WHERE A.wr_etc_use='0' AND A.wr_direct_use='1' AND A.wr_product_id='".$wr_id."' AND A.wr_warehouse='".$warehouse."' AND IFNULL(B.wr_release_use,'0') = '0'";
			$total_sales_data = sql_fetch($sql);
			$total_sales_ea = $total_sales_data['total_sales_ea'];

      if ($field) {
        $sql = "UPDATE g5_write_product SET {$field} = {$rack_stock}, {$field_real} = {$rack_stock} + {$total_sales_ea} WHERE wr_id='".$wr_id."'";
        $rs = sql_query($sql);
        if(!$rs){
          $db_flag = false;
        }
      }
		}else{
			$db_flag = false;
		}
	}

	if($db_flag == true){
		$msg = "재고 변경이 완료되었습니다.";
		sql_trans_commit();
	}else{
		$msg = "재고 변경에 실패하였습니다.";
		sql_trans_rollback();
	}
	
	alert($msg);
}

?>