<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

if(!$_POST['wr_id']) alert('잘못 된 접근입니다.');

// 01-11 = 기초재고 랙 이동에 대한 수정이 없어서 FBA 반영이 안되어 있음.
if($mode == "move1_kor") {
	#미분류 재고 랙 이동 - 한국
	
	//이동창고 재고증감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '1000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[한국창고(1000)] 미분류 재고가 랙으로 이동되었습니다.');
	
} else if($mode == "move1_usa") {
	#미분류 재고 랙 이동 - 미국 
	
	//이동창고 재고증감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '3000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[미국창고(3000)] 미분류 재고가 랙으로 이동되었습니다.');
	
	
} else if($mode == "move2") {
	#일반재고 랙 이동
	
	$ms_stock = trim($_POST['ms_stock']);
	$ms_stock = (int)$ms_stock;
	
	//대상창고 재고차감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse}', wr_rack = '{$ms_rack}', wr_stock = '-{$ms_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	//이동창고 재고증감 
	$sql2 = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse2}', wr_rack = '{$ms_rack2}', wr_stock = '{$ms_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql2);
	
	//이동후 전체재고 다시 계산 
	/* g5_rack_stock의 데이터가 틀어져있음으로 g5_rack_stock 합산 사용금지
	$stock_kor = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$wr_id}'"); //한국 전체 재고
	
	$stock_kor2 = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '7000' and wr_product_id = '{$wr_id}'"); //한국 전체 재고
	
	$stock_usa = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '3000' and wr_product_id = '{$wr_id}'"); //미국 전체 재고
	
	$stock_usa2 = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '8000' and wr_product_id = '{$wr_id}'"); //미국 전체 재고
	
	$stock_kor = (int)$stock_kor['total'];//한국창고
	$stock_kor2 = (int)$stock_kor2['total'];//한국반품창고
	$stock_usa = (int)$stock_usa['total'];//미국창고
	$stock_usa2 = (int)$stock_usa2['total'];//미국반품창고
	*/

	$minus_field = $storage_arr[$ms_warehouse]['field'];
	$plus_field = $storage_arr[$ms_warehouse2]['field'];

	$minus_field_real = $storage_arr[$ms_warehouse]['field_real'];
	$plus_field_real = $storage_arr[$ms_warehouse2]['field_real'];
	
	//전체재고 상품마스터에 다시 업데이트 
	//sql_query("update g5_write_product set wr_32 = '{$stock_kor}', wr_36 = '{$stock_usa}', wr_40 = '{$stock_kor2}', wr_41 = '{$stock_usa2}',{$minus_field_real} = {$minus_field_real} - {$ms_stock},{$plus_field_real} = {$plus_field_real} + {$ms_stock} where wr_id = '{$wr_id}'");
	sql_query("update g5_write_product set {$minus_field} = {$minus_field} - {$ms_stock},{$plus_field} = {$plus_field} + {$ms_stock},{$minus_field_real} = {$minus_field_real} - {$ms_stock},{$plus_field_real} = {$plus_field_real} + {$ms_stock} where wr_id = '{$wr_id}'");
	
	
	alert('재고가 선택하신 창고와 랙으로 이동되었습니다.');

} else if($mode == "indi") {
	/*
	Array
	(
		[mode] => indi
		[wr_id] => 15280 -> 상품 번호
		[warehouse] => 1000 -> 창고
		[seq] => 306 -> 랙번호
		[ori_qty] => 3 -> 실재고 - 출고예정수량
		[sales_stock] => 6 출고 예정수량
		[qty] => 20 -> 변경된 수량
	)
	*/


	
	# 트랜잭션 시작
	sql_trans_start();
	
	# 데이터 INSERT / UPDATE 성공 여부
	$db_flag = true;

	/*
	1. 해당 재고에서 출고예정이 수량 조회
	2. 실제 재고 수량 ( 출고예정포함 )
	3. 차이나는 수량 계산
	4. g5_rack_stock에 차이 등록
	5. 상품의 창고별 총 재고 수량 합산
	6. 상품의 창고별 총 재고 업데이트
	*/

	# 해당랙의 상품 출고 예정 수량
	$sql = "SELECT IFNULL(SUM(A.wr_ea),0) AS sales_stock FROM g5_sales2_list A \n";
	$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num \n";
	$sql .= "WHERE A.wr_etc_use='0' AND A.wr_direct_use='1' AND A.wr_rack='".$seq."' AND A.wr_product_id='".$wr_id."' AND A.wr_warehouse='".$warehouse."' AND IFNULL(B.wr_release_use,'0') = '0'";
	$salesData = sql_fetch($sql);
	$sales_ea = $salesData['sales_stock'];

	$total_stock = $ori_qty + $sales_ea; // 기존 랙의 수량 전체 ( 출고예정수량 포함 )
	$calc_stock = $qty - $total_stock; // 변경 수량과 기존 랙의 수량전체 차이
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
			# 상품의 창고별 전체 재고 조회( 출고 예정 데이터가 차감된 상태 )
			$sql = "SELECT IFNULL(SUM(wr_stock),0) AS rack_stock FROM g5_rack_stock WHERE wr_product_id='".$wr_id."' AND wr_warehouse='".$warehouse."'";
			$rack_stock_data = sql_fetch($sql);
			$rack_stock = $rack_stock_data['rack_stock'];

			# 변경 되는 상품의 관련 창고 필드
			$field = $storage_arr[$warehouse]['field'];
			$field_real = $storage_arr[$warehouse]['field_real'];

			# 상품의 전체 출고예정 수량
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