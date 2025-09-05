<?
include_once('../common.php');

# 결과 리턴
$return = array();
$return['ret_code'] = true;
$return['message'] = "";
if(!$member['mb_id']){
	$return['ret_code'] = false;
	$return['message'] = "로그인 후 이용해주세요.";
	echo json_encode($return);
	exit;
}

# 파라메터 받기
$wr_warehouse = $rack_warehouse;

# 업데이트 관련 처리 갯수 정리
$total = 0;
$succ = 0;
$fail = 0;
foreach((array)$report_stock_arr as $key => $val){
	if($wr_warehouse != "9000"){ // 임시창고가 아닐 경우
		$key_arr = explode("|",$key);
		$rack_seq = $key_arr[0];
		$wr_id = $key_arr[1];
		$real_stock = $real_stock_arr[$key];
		$stock = $stock_arr[$key];
		$report_stock = $val;

		$sql = "SELECT SUM(A.wr_ea) AS wr_ea FROM g5_sales2_list A \n";
		$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.sales2_id = A.seq \n";
		$sql .= "WHERE A.wr_warehouse = '".$wr_warehouse."' AND A.wr_product_id='".$wr_id."' AND A.wr_direct_use='1' AND A.wr_etc_use='0' AND IFNULL(B.wr_release_use,0) = 0";
		$sumData = sql_fetch($sql);

		# 실사 재고 수량이 0보다 크거나 같을 경우에만 적용
		if($val != ""){
			$calc_stock = (int)$report_stock - (int)$real_stock;
			# 실사 재고와 기록된 재고의 수량이 차이가 없을 경우 데이터 기록하지 않음
			if($calc_stock != 0){
				$calc_stock_conv = $report_stock - $calc_stock;

				# 재고 로그에 실사 재고 차이만큼 기록
				$sql = "INSERT INTO g5_rack_stock SET \n";
				$sql .= "	wr_warehouse = '".$wr_warehouse."' \n";
				$sql .= "	,wr_rack = '".$rack_seq."' \n";
				$sql .= "	,wr_stock = '".$calc_stock."' \n";
				$sql .= "	,wr_product_id = '".$wr_id."' \n";
				$sql .= "	,wr_sales3_id = 0 \n";
				$sql .= "	,wr_mb_id = '".$member['mb_id']."' \n";
				$sql .= "	,wr_datetime = NOW() \n";
				$sql .= "	,wr_move_log = '실사재고 업데이트'";
				$rs = sql_query($sql);

				if($rs){
					if($rack_seq == 1){
						$field = $storage_arr['9000']["field"];
						$field_real = $storage_arr['9000']["field_real"];
					}else{
						$field = $storage_arr[$wr_warehouse]["field"];
						$field_real = $storage_arr[$wr_warehouse]["field_real"];
					}

					# 상품의 전체 재고 조회( 재고 로그의 기록 가져오기 )
					$sql = "SELECT SUM(wr_stock) AS total_stock FROM g5_rack_stock WHERE wr_warehouse='".$wr_warehouse."' AND wr_product_id='".$wr_id."'";
					$totalData = sql_fetch($sql);
					$total_stock = $totalData['total_stock'];

					$field_calc = $total_stock + $sumData['wr_ea'];

					# 수정된 재고 상품에 업데이트
					$sql = "UPDATE g5_write_product SET {$field} = ".$total_stock.", {$field_real} = '".$field_calc."' WHERE wr_id='".$wr_id."'";
					$rs = sql_query($sql);
					if($rs){
						$succ++;
					}else{
						$fail++;
					}
				}else{
					$fail++;
				}
			}else{
				if($rack_seq == 1){
					$field = $storage_arr['9000']["field"];
					$field_real = $storage_arr['9000']["field_real"];
				}else{
					$field = $storage_arr[$wr_warehouse]["field"];
					$field_real = $storage_arr[$wr_warehouse]["field_real"];
				}

				# 상품의 전체 재고 조회( 재고 로그의 기록 가져오기 )
				$sql = "SELECT SUM(wr_stock) AS total_stock FROM g5_rack_stock WHERE wr_warehouse='".$wr_warehouse."' AND wr_product_id='".$wr_id."'";
				$totalData = sql_fetch($sql);
				$total_stock = $totalData['total_stock'];

				$field_calc = $total_stock + $sumData['wr_ea'];

				# 수정된 재고 상품에 업데이트
				$sql = "UPDATE g5_write_product SET {$field} = ".$total_stock.", {$field_real} = '".$field_calc."' WHERE wr_id='".$wr_id."'";
				$rs = sql_query($sql);
				if($rs){
					$succ++;
				}else{
					$fail++;
				}
			}
			$total++;
		}
	}else{ // 임시창고의 경우
		if($val != ""){
			$wr_id = $key;
			$real_stock = $real_stock_arr[$key]; // 총재고
			$sales_stock = $sales_stock_arr[$key]; // 출고예정
			$stock = $stock_arr[$key]; // 출고예정 제외 잔여재고
			$report_stock = $val; // 실사재고 입력값

			// 실사재고 - 총재고
			$calc_stock = $report_stock - $real_stock;
			if($calc_stock != 0){
				$sql = "INSERT INTO g5_temp_warehouse SET \n";
				$sql .= "	sales1_id = '0' \n";
				$sql .= "	,sales2_id = '0' \n";
				$sql .= "	,wr_stock = '".$calc_stock."' \n";
				$sql .= "	,wr_stock2 = 0 \n";
				$sql .= "	,wr_rack = '' \n";
				$sql .= "	,wr_product_id = '".$wr_id."' \n";
				$sql .= "	,wr_state = '0' \n";
				$sql .= "	,wr_report_chk = 'Y' \n";
				$sql .= "	,wr_mb_id = '".$member['mb_id']."' \n";
				$sql .= "	,wr_datetime = NOW() \n";
				$sql .= "	,wr_log = '실사재고 업데이트'";
				$rs = sql_query($sql);
				
				if($rs){
					$sql = "UPDATE g5_write_product SET \n";
					$sql .= "	wr_37 = '".($report_stock - $sales_stock)."' \n";
					$sql .= "	,wr_37_real = '".$report_stock."' \n";
					$sql .= "WHERE wr_id='".$wr_id."'";
					$rs = sql_query($sql);
					if($rs){
						$succ++;
					}else{
						$fail++;
					}
				}else{
					$fail++;
				}
			}else{
				$succ++;
			}
			$total++;
		}
	}
}
$msg = "전체 : ".number_format($total)." / 성공 : ".number_format($succ)." / 실패 : ".number_format($fail);
$return['message']  = $msg;
echo json_encode($return);
exit;
?>