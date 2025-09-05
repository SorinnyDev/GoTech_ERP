<?php 
include_once('./_common.php');

if($is_guest) alert('로그인 후 이용하세요.');

$post_count_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;
$chk            = (isset($_POST['chk']) && is_array($_POST['chk'])) ? $_POST['chk'] : array();
$act_button     = isset($_POST['act']) ? strip_tags($_POST['act']) : '';

if (!$post_count_chk) {
    alert($act_button . " 하실 항목을 하나 이상 체크하세요.");
}
$suc_cnt = 0;
$fail_cnt = 0;

if ($act_button === "일괄재고이동") {	
	for ($i = 0; $i < $post_count_chk; $i++) {
		// 실제 번호를 넘김
		$k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;
		
		$wr_id = (int)$_POST['wr_id'][$k];
		$seq = (int)$_POST['chk_seq'][$k];
		$sid = (int)$_POST['sales2_id'][$k];
		$warehouse = (int)$_POST['warehouse'][$k];
		$stock = (int)$_POST['wr_37'][$k];
		$rack = $_POST['wr_rack'][$k];
		
		if($warehouse == '1000') {
			$filed = 'wr_32'; //한국
			$filed_real = 'wr_32_real';
			$warehouse_name = "한국창고";
		} else if($warehouse == '3000') {
			$filed = 'wr_36'; //미국
			$filed_real = 'wr_36_real';
			$warehouse_name = "미국창고";
		}
		
		$chk = sql_fetch("select * from g5_temp_warehouse where tw_seq = '{$seq}'");
		
		/*24.05.30 업데이트로 필요없어짐.
		if($chk['wr_state'] == 0 && !$rack) {
			
			
			
			
			$rack_info = sql_fetch("select seq from g5_rack where gc_warehouse = '{$warehouse}' and gc_name ='임시창고'");
			
			//처리하지 않고 랙을 선택하지 않았을때 한국/미국창고 입고자료가져오기로 이동.
			$sql2 = "update g5_temp_warehouse set wr_stock2 = '{$stock}', wr_state = 1, wr_state_mbid = '{$member['mb_id']}', wr_state_date = '".G5_TIME_YMDHIS."', wr_rack = '{$rack_info['seq']}' where tw_seq = '{$seq}'";
			sql_query($sql2, true);
			
			//지정창고 이동 : 24.05.12 post[sid] 에서 chk[sales2_id]로 변경
			$sql3 = "update g5_sales2_list set wr_warehouse = '{$warehouse}', wr_rack = '{$rack_info['seq']}' where seq = '{$chk['sales2_id']}' ";
			sql_query($sql3, true);
		
			
			//한국/미국 창고 재고 증감 및 임시창고 차감
			$sql = "update g5_write_product set {$filed} = {$filed} + {$stock}, wr_37 = wr_37 - {$stock} where wr_id = '{$wr_id}' limit 1";
			sql_query($sql, true);
			
			//재고이관 기록 240517
			$sql4 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack_info['seq']}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
			sql_query($sql4, true);
		}*/
		
		if($rack && $chk['wr_stock'] >= 1) {
			//랙 선택시 
			
			/*//한국/미국 창고 재고 증감 및 임시창고 차감
			$sql = "update g5_write_product set {$filed} = {$filed} + {$stock}, wr_37 = wr_37 - {$stock} where wr_id = '{$wr_id}' limit 1";
			sql_query($sql, true);
			
			
			
			$sql4 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
			sql_query($sql4);
			*/
			
			
			//랙 선택시 
			$sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}',wr_sales3_id = '{$chk['sales2_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '임시창고 > 입고'";
			sql_query($sql);
			
			//한국/미국 창고 재고 증감 및 임시창고 차감
			$sql2 = "update g5_write_product set {$filed} = {$filed} + {$stock}, {$filed_real} = {$filed_real} + {$stock}, wr_37 = wr_37 - {$stock}, wr_37_real = wr_37_real - {$stock} where wr_id = '{$wr_id}' limit 1";
			sql_query($sql2);
			
			//로그 기록
			$log = $member['mb_name'].'('.$member['mb_id'].') '.G5_TIME_YMDHIS.' '.$warehouse_name.' 로 '.$stock.'개 재고이동\n';
			
			//임시창고 재고 차감
			$sql3 = "update g5_temp_warehouse set wr_stock = wr_stock - {$stock}, wr_log = CONCAT(wr_log, '{$log}') where tw_seq = '{$seq}'";
			sql_query($sql3);
			
			$suc_cnt++;
		} else {
			$fail_cnt++;
		}
		
	}
	
	alert('일괄 재고이동이 완료되었습니다.\\n성공 : '.$suc_cnt.'건\\n실패 : '.$fail_cnt.'건');
	
} else if($act_button === "일괄재고삭제"){
	if(count((array)$chk_seq) == 0){
		alert("선택된 재고가 없습니다.");
		exit;
	}

	$total = 0;
	$succ = 0;
	$fail = 0;
	$error = 0;
	foreach((array)$chk as $key => $val){
		$chk_val = $chk_seq[$val];
		$sql = "SELECT A.*,B.seq FROM g5_temp_warehouse A \n";
		$sql .= "LEFT OUTER JOIN g5_sales2_list B ON B.seq=A.sales2_id \n";
		$sql .= "WHERE A.tw_seq='".$chk_val."'";
		$row = sql_fetch($sql);
		if($row['seq']){
			$fail++;
			continue;
		}else{
			$sql = "DELETE FROM g5_temp_warehouse WHERE tw_seq='".$chk_val."'";
			$rs = sql_query($sql);
			if($rs){
				$sql = "UPDATE g5_write_product SET wr_37= wr_37-".$row['wr_stock'].",wr_37_real= wr_37_real-".$row['wr_stock']." WHERE wr_id='".$row['wr_product_id']."'";
				sql_query($sql);
				$succ++;
			}else{
				$error++;
			}
		}

		$total++;
	}

	$msg = "전체 : ".number_format($total)."(성공 : ".number_format($succ)." / 실패 : ".number_format($fail)." / 오류 : ".number_format($error).")";
	alert($msg);
} else {
	alert('잘못 된 접근입니다.');
}