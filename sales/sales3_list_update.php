<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

$count = (isset($_POST['seq']) && is_array($_POST['seq'])) ? count($_POST['seq']) : 0;

if(!$count) {
    alert('삭제 하실 항목을 하나 이상 선택하세요.');
}

if($_POST['btn_submit']=="선택삭제"){
	$msg = "[출고등록] 데이터가 삭제되었습니다." ;
}else if($_POST['btn_submit']=="완전삭제"){
	$msg = "[출고등록] 데이터가 완전히 삭제되었습니다.";
}else if($_POST['btn_submit']=="출고일괄처리"){
	$msg = "[출고등록] 데이터가 일괄 출고 완료 처리 되었습니다.";
}


for($i=0; $i<$count; $i++) {
    $obj = sql_fetch("select * from g5_sales3_list where seq = '{$_POST['seq'][$i]}'");

    if($_POST['btn_submit']=="선택삭제"){
        
		if($obj['wr_direct_use'] == 1){
			$field = $storage_arr[$obj['wr_warehouse']]['field'];
			$field_real = $storage_arr[$obj['wr_warehouse']]['field_real'];

			if($field != ""){
				# 실제 재고 복구
				if($obj['wr_release_use'] == "1"){
					$sql = "UPDATE g5_write_product SET {$field_real} = {$field_real} + {$obj['wr_ea']} WHERE wr_id='{$obj['wr_product_id']}'";
					sql_query($sql);
				}

				$sql = "delete from g5_sales3_list where seq = '{$_POST['seq'][$i]}' LIMIT 1";
				$rs = sql_query($sql);

				$sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('".addslashes($sql)."','".addslashes(json_encode($_REQUEST))."','".$member['mb_id']."',NOW(),'".$obj['wr_order_num']."')";
				sql_query($sql);
				
				if($rs){
					#sql_query("update g5_sales2_list set wr_chk = 0 where seq = '{$obj['wr_id']}' limit 1");
					# 매출등록시 바로 출고의 경우 이전 단계가 엑셀자료 가져오기 임으로 해당단계까지 데이터 삭제
					sql_query("UPDATE g5_sales2_list SET wr_chk='0' WHERE wr_order_num = '{$obj['wr_order_num']}'");
				}
			}

		}else{

			# 이전 단계 처리 안함으로 수정
			sql_query("UPDATE g5_sales2_list SET wr_chk = 0 WHERE wr_order_num = '{$obj['wr_order_num']}'");
			# 출고로 잡혔던 재고 복구 wr_release_use가 1일 경우 이미 출고완료가 되었다는 의미임으로 실재고만 복구
			if($obj['wr_release_use'] == "1"){
				if($obj['wr_warehouse'] == "3000"){
					$filed = $storage_arr[$obj['wr_warehouse']]['field'];
					$filed_real = $storage_arr[$obj['wr_warehouse']]['field_real'];
					sql_query("update g5_write_product set {$filed_real} = {$filed_real} + {$obj['wr_ea']} where wr_id = '{$obj['wr_product_id']}'");
				}else{
					sql_query("update g5_write_product set wr_37_real = wr_37_real + {$obj['wr_ea']} where wr_id = '{$obj['wr_product_id']}'");
				}
			}
			# 이전 단계 미처리로 변경
			$sql = "UPDATE g5_sales2_list SET wr_chk  = 0 WHERE wr_order_num = '{$obj['wr_order_num']}'";
			sql_query($sql);

			# 출고 데이터 삭제
			$sql = "DELETE FROM g5_sales3_list WHERE seq='{$_POST['seq'][$i]}'";
			sql_query($sql);

			$sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('".addslashes($sql)."','".addslashes(json_encode($_REQUEST))."','".$member['mb_id']."',NOW(),'".$obj['wr_order_num']."')";
			sql_query($sql);
		}

		# 바로 출고 / 발주 후 출고 상관없이 선입선출 정보 복원
		$sql = "SELECT * FROM g5_sales3_det WHERE del_yn = 'N' AND sales3_id='".$_POST['seq'][$i]."'";
		$ibRs = sql_query($sql);
		while($ibRow = sql_fetch_array($ibRs)){
			# 해당 주문건의 선입 단가의 출고 수량 복구
			$sql = "UPDATE g5_sales2_list SET wr_chul_ea = wr_chul_ea + ".$ibRow['chul_ea']." WHERE seq='".$ibRow['sales2_id']."'";
			sql_query($sql);

			# 해당 선입 선출 데이터 del_yn을 Y(삭제)로 업데이트
			$sql = "UPDATE g5_sales3_det SET del_yn = 'Y' WHERE idx='".$ibRow['idx']."'";
			sql_query($sql);
		}
	
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
					# 상품 재고 복구
					$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']}, {$field_real} = {$field_real} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
				}else{
					# 상품 재고 복구
					$sql = "UPDATE g5_write_product SET {$field} = {$field} + {$row3['wr_ea']} WHERE wr_id = '{$row3['wr_product_id']}'";
					sql_query($sql);
				}

				# 랙 재고 로그 복구
				$sql = "INSERT INTO g5_rack_stock SET wr_warehouse = '{$row3['wr_warehouse']}', wr_rack = '{$row3['wr_rack']}', wr_stock = '{$row3['wr_ea']}', wr_product_id = '{$row3['wr_product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '출고 삭제로 인한 재고 복구'";
				sql_query($sql);
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
			}
		}

		# 바로 출고 / 발주 후 출고 상관없이 선입선출 정보 복원
		$sql = "SELECT * FROM g5_sales3_det WHERE del_yn = 'N' AND sales3_id='".$_POST['seq'][$i]."'";
		$ibRs = sql_query($sql);
		while($ibRow = sql_fetch_array($ibRs)){
			# 해당 주문건의 선입 단가의 출고 수량 복구
			$sql = "UPDATE g5_sales2_list SET wr_chul_ea = wr_chul_ea + ".$ibRow['chul_ea']." WHERE seq='".$ibRow['sales2_id']."'";
			sql_query($sql);

			# 해당 선입 선출 데이터 del_yn을 Y(삭제)로 업데이트
			$sql = "UPDATE g5_sales3_det SET del_yn = 'Y' WHERE idx='".$ibRow['idx']."'";
			sql_query($sql);
		}

        sql_query("delete from g5_write_sales where wr_subject = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales0_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales1_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales2_list where wr_order_num = '{$obj['wr_order_num']}' ");
        sql_query("delete from g5_sales3_list where wr_order_num = '{$obj['wr_order_num']}' ");
        
        sql_query("UPDATE g5_board SET bo_count_write = bo_count_write - 1 WHERE bo_table = 'sales' ");

        // $seq0 = sql_fetch("select wr_id from g5_sales2_list where seq = '{$obj['wr_id']}'")['wr_id'];
        // $wr_seq = sql_fetch("select wr_id from g5_sales0_list where seq = '{$seq0}'")['wr_id'];

        //echo $wr_seq." ".$seq0." ".$obj['wr_id']." ".$_POST['seq'][$i];
        // sql_query("delete from g5_write_sales where wr_id = '{$wr_seq}' ");         //매출자료 관리
        // sql_query("delete from g5_sales0_list where seq = '{$seq0}' ");             //매출 관리
        // sql_query("delete from g5_sales2_list where seq = '{$obj['wr_id']}' ");     //입고 관리 
        // sql_query("delete from g5_sales3_list where seq = '{$_POST['seq'][$i]}' "); //출고 관리
    
    }else if($_POST['btn_submit']=="출고일괄처리"){
		####################################################
		# 실재고 조사를 위해 실재고관련 필드 업데이트
		####################################################
		$filed = $storage_arr[$obj['wr_warehouse']]['field_real'];
		if($obj['wr_release_use'] != "1"){
			if($obj['wr_direct_use'] == 1 || $obj['wr_warehouse'] == "3000") {
				sql_query("update g5_write_product set {$filed} = {$filed} - {$obj['wr_ea']} where wr_id = '{$obj['wr_product_id']}'");
			}else{
				sql_query("update g5_write_product set wr_37_real = wr_37_real - {$obj['wr_ea']} where wr_id = '{$obj['wr_product_id']}'");
			}
		}
		
		# 출고 완료 처리
		$sql = "UPDATE g5_sales3_list SET wr_release_use = '1' WHERE seq='".$obj['seq']."'";
		sql_query($sql);
	}
}

alert($msg);