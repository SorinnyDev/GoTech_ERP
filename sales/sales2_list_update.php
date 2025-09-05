<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

$count = (isset($_POST['seq']) && is_array($_POST['seq'])) ? count($_POST['seq']) : 0;

if(!$count) {
    alert('삭제 하실 항목을 하나 이상 선택하세요.');
}


$msg = ($_POST['btn_submit']=="선택삭제") ? "[입고등록] 데이터가 삭제되었습니다." : "[입고등록] 데이터가 완전히 삭제되었습니다.";
$cancel_arr = array();
$total = $count;
$succ = 0;
$fail = 0;
for($i=0; $i<$count; $i++) {
    $obj = sql_fetch("select * from g5_sales2_list where seq = '{$_POST['seq'][$i]}'");
    $excel = sql_fetch("select * from g5_sales1_list where wr_order_num = '{$obj['wr_order_num']}'");

    if($_POST['btn_submit']=="선택삭제"){


		# 출고 데이터 불러오기
		# 출고 데이터가 있을 경우 출고 데이터 삭제 및 출고 상태값에 따른 재고 복구
		$sql = "SELECT * FROM g5_sales3_list WHERE wr_order_num='{$obj['wr_order_num']}'";
		$row3 = sql_fetch($sql);
		$field = $storage_arr[$row3['wr_warehouse']]['field'];
		$field_real = $storage_arr[$row3['wr_warehouse']]['field_real'];
		
		if($excel['wr_etc_chk'] == "0"){

			if($row3['seq']){
				# wr_direct_use가 1일 경우 매출등록에서 바로 출고등록으로 온 경우
				# wr_direct_use가 0일 경우 재고가 없어 발주단계부터 순서대로 넘어온 경우
				if($row3['wr_direct_use'] == 1){
					# wr_release_use가 1일 경우 이미 출고 완료가 되었음으로 실제 재고와 가상(출고 예상 포함) 재고를 복구
					# wr_release_use가 0일 경우 출고 완료가 되지않았음으로 가상(출고 예상 포함) 재고만 복구
					if($row3['wr_release_use'] == "1"){
						$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']}, {$field_real} = {$field_real} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
					}else{
						$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
						
					}
					# 랙재고 로그에 출고 삭제로 인한 재고 복구 데이터 입력
					$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row3['wr_warehouse']}', wr_rack = '{$row3['wr_rack']}', wr_stock = '{$row3['wr_ea']}', wr_product_id = '{$row3['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
					sql_query($sql);
				}else if($row3['wr_direct_use'] != "1" && $row3['wr_warehouse'] == "3000"){
					# wr_release_use가 0일 경우 출고 완료 전( 출고 예상재고만 복원 )
					if($row3['wr_release_use'] == "1"){
						$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']}, {$field_real} = {$field_real} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
					}else{
						$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
					}

					# 랙재고 로그에 출고 삭제로 인한 재고 복구 데이터 입력
					$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row3['wr_warehouse']}', wr_rack = '{$row3['wr_rack']}', wr_stock = '{$row3['wr_ea']}', wr_product_id = '{$row3['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
					sql_query($sql);
				}else{
					# wr_direct_use가 0일 경우 임시창고에서 바로 출고가 됨으로 임시창고를 업데이트
					if($row3['wr_release_use'] == "1"){
						$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row3['wr_ea']}, wr_37_real = wr_37_real + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
					}else{
						$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);
					}

					$sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock + {$row3['wr_ea']}, wr_stock2 = wr_stock2 - {$row3['wr_ea']} WHERE sales2_id = '{$row3['wr_id']}'";
					sql_query($sql);
				}

				# 바로 출고 / 발주 후 출고 상관없이 선입선출 정보 복원
				$sql = "SELECT * FROM g5_sales3_det WHERE del_yn = 'N' AND sales3_id='".$row3['seq']."'";
				$ibRs = sql_query($sql);
				while($ibRow = sql_fetch_array($ibRs)){
					# 해당 주문건의 선입 단가의 출고 수량 복구
					$sql = "UPDATE g5_sales2_list SET wr_chul_ea = wr_chul_ea + ".$ibRow['chul_ea']." WHERE seq='".$ibRow['sales2_id']."'";
					sql_query($sql);

					# 해당 선입 선출 데이터 del_yn을 Y(삭제)로 업데이트
					$sql = "UPDATE g5_sales3_det SET del_yn = 'Y' WHERE idx='".$ibRow['idx']."'";
					sql_query($sql);
				}

				$sql = "DELETE FROM g5_sales3_list WHERE seq='{$row3['seq']}'";
				sql_query($sql);

				$sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('".addslashes($sql)."','".addslashes(json_encode($_REQUEST))."','".$member['mb_id']."',NOW(),'".$obj['wr_order_num']."')";
				sql_query($sql);
			}

			# 입고 데이터 불러오기
			$sql2 = "SELECT * FROM g5_sales2_list WHERE wr_order_num = '".$obj['wr_order_num']."'";
			$row2 = sql_fetch($sql2);
			# 바로 출고일 경우 실제 발주 및 입고가 이루어 지지 않았음으로 차감하지 않음
			# 바로 출고가 아닐 경우 입고 되는 순간 바로 차감이 이루어 짐으로 출고된 재고 복구
			if( $row2['seq']){

				# 수정 필드 배열에서 가져오기
				$field = $storage_arr[$row2['wr_warehouse']]['field'];
				$field_real = $storage_arr[$row2['wr_warehouse']]['field_real'];

				if($row2['wr_direect_use'] != "1" && !$row3['seq']){
					if($row2['wr_warehouse'] == "3000"){
						$sql = "UPDATE g5_write_product SET {$field_real} = {$field_real} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
						sql_query($sql);

						# 랙재고 로그에 출고 삭제로 인한 재고 복구 데이터 입력
						$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row2['wr_warehouse']}', wr_rack = '{$row2['wr_rack']}', wr_stock = '{$row2['wr_ea']}', wr_product_id = '{$row2['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
						sql_query($sql);
					}else{
						$sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock + {$row2['wr_ea']}, wr_stock2 = wr_stock2 - {$row2['wr_ea']} WHERE sales2_id = '{$row2['seq']}'";
						sql_query($sql);

						$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row2['wr_ea']} WHERE wr_id = '{$row2['wr_product_id']}'";
						sql_query($sql);
					}
				}

				$sql = "DELETE FROM g5_sales2_list WHERE seq='{$row2['seq']}'";
				sql_query($sql);

				$sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('".addslashes($sql)."','".addslashes(json_encode($_REQUEST))."','".$member['mb_id']."',NOW(),'".$obj['wr_order_num']."')";
				sql_query($sql);
			}
		}else{
			$sql2 = "SELECT * FROM g5_sales2_list WHERE wr_order_num = '".$obj['wr_order_num']."'";
			$row2 = sql_fetch($sql2);
			if($row2['seq']){
				$sql = "DELETE FROM g5_sales2_list WHERE seq='{$row2['seq']}'";
				sql_query($sql);

				$sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('".addslashes($sql)."','".addslashes(json_encode($_REQUEST))."','".$member['mb_id']."',NOW(),'".$obj['wr_order_num']."')";
				sql_query($sql);
			}
		}

		sql_query("update g5_sales1_list set wr_chk = 0 where wr_order_num = '{$obj['wr_order_num']}' limit 1");

    }else if($_POST['btn_submit']=="완전삭제"){

		# 출고 데이터 불러오기
		$sql3 = "SELECT * FROM g5_sales3_list WHERE wr_order_num = '".$obj['wr_order_num']."'";
		$row3 = sql_fetch($sql3);
		$field = $storage_arr[$row3['wr_warehouse']]['field'];
		$field_real = $storage_arr[$row3['wr_warehouse']]['field_real'];

		# 출고 데이터 복구
		if($row3['seq']){
			# 바로 출고 등록일 경우 
			if($row3['wr_direct_use'] == "1"){
				if($row3['wr_release_use'] == "1"){
					$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']}, {$field_real} = {$field_real} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
					$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row3['wr_warehouse']}', wr_rack = '{$row3['wr_rack']}', wr_stock = '{$row3['wr_ea']}', wr_product_id = '{$row3['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '입고 삭제로 인한 재고 복구'";
					sql_query($sql);
				}else{
					$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
					$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row3['wr_warehouse']}', wr_rack = '{$row3['wr_rack']}', wr_stock = '{$row3['wr_ea']}', wr_product_id = '{$row3['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
					sql_query($sql);
				}
			# 재고가 없어서 바로 출고가 아닐 경우
			}else{
				# 실재고 차감 복구 및 매출 예상 재고 복구
				if($row3['wr_release_use'] == "1"){
					$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row3['wr_ea']}, wr_37_real = wr_37_real + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
				# 매출 예상 재고 복구
				}else{
					$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
				}

				# 임시창고 데이터 복구
				$sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock - {$row2['wr_ea']}, wr_stock2 = wr_stock2 + {$row2['wr_ea']} WHERE sales2_id = '{$row3['wr_id']}'";
				sql_query($sql);
			}

			# 바로 출고 / 발주 후 출고 상관없이 선입선출 정보 복원
			$sql = "SELECT * FROM g5_sales3_det WHERE del_yn = 'N' AND sales3_id='".$row3['seq']."'";
			$ibRs = sql_query($sql);
			while($ibRow = sql_fetch_array($ibRs)){
				# 해당 주문건의 선입 단가의 출고 수량 복구
				$sql = "UPDATE g5_sales2_list SET wr_chul_ea = wr_chul_ea + ".$ibRow['chul_ea']." WHERE seq='".$ibRow['sales2_id']."'";
				sql_query($sql);

				# 해당 선입 선출 데이터 del_yn을 Y(삭제)로 업데이트
				$sql = "UPDATE g5_sales3_det SET del_yn = 'Y' WHERE idx='".$ibRow['idx']."'";
				sql_query($sql);
			}
		}
		
		# 입고 데이터 불러오기
		$sql2 = "SELECT * FROM g5_sales2_list WHERE wr_order_num = '".$obj['wr_order_num']."'";
		$row2 = sql_fetch($sql2);
		
		# 바로 출고일 경우 실제 발주 및 입고가 이루어 지지 않았음으로 차감하지 않음
		if( $row2['seq']){

			if($row2['wr_direect_use'] != "1" && !$row3['seq']){
				$sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock + {$row2['wr_ea']}, wr_stock2 = wr_stock2 - {$row2['wr_ea']} WHERE sales2_id = '{$row2['seq']}'";
				sql_query($sql);

				$sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$row2['wr_ea']} WHERE wr_id = '{$row2['wr_product_id']}'";
				sql_query($sql);
			}else if($row2['wr_direct_use'] == "1" && !$row3['seq']){
				$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row2['wr_ea']} WHERE wr_id = '{$row2['wr_product_id']}'";
				sql_query($sql);

				# 랙재고 로그에 출고 삭제로 인한 재고 복구 데이터 입력
				$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row2['wr_warehouse']}', wr_rack = '{$row2['wr_rack']}', wr_stock = '{$row2['wr_ea']}', wr_product_id = '{$row2['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
				sql_query($sql);
			}
		}

        sql_query("delete from g5_write_sales where wr_subject = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales0_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales1_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales2_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales3_list where wr_order_num = '{$obj['wr_order_num']}' ");

        sql_query("UPDATE g5_board SET bo_count_write = bo_count_write - 1 WHERE bo_table = 'sales' ");
        // $seq0 = sql_fetch("select wr_id from g5_sales1_list where seq = '{$obj['wr_id']}'")['wr_id'];
        // $wr_seq = sql_fetch("select wr_id from g5_sales0_list where seq = '{$seq0}'")['wr_id'];

        // echo $wr_seq." ".$seq0." ".$obj['wr_id']." ".$_POST['seq'][$i];

        // sql_query("delete from g5_write_sales where wr_id = '{$wr_seq}' ");         //매출자료 관리
        // sql_query("delete from g5_sales0_list where seq = '{$seq0}' ");             //매출 관리
        // sql_query("delete from g5_sales1_list where seq = '{$obj['wr_id']}' ");     //입고 관리
        // sql_query("delete from g5_sales2_list where seq = '{$_POST['seq'][$i]}' ");     //입고 관리

    }
}

if(count($cancel_arr) > 0){
	$msg = "삭제 불가능한 데이터가 있습니다.";
}
$msg .= "(전체 : ".number_format($total)." / 성공 : ".number_format($total - $fail)." / 실패 : ".number_format($fail).")";
alert($msg);