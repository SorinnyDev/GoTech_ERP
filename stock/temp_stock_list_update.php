<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

$post_count_chk = (isset($_POST['chk']) && is_array($_POST['chk'])) ? count($_POST['chk']) : 0;
$chk            = (isset($_POST['chk']) && is_array($_POST['chk'])) ? $_POST['chk'] : array();
$act_button     = isset($_POST['act']) ? strip_tags($_POST['act']) : '';

if (!$post_count_chk) {
	alert($act_button . " 하실 항목을 하나 이상 체크하세요.");
}
$suc_cnt = 0;
$fail_cnt = 0;

if ($act_button === "일괄재고이동") {
	$no_expired_index = [];

	for ($i = 0; $i < $post_count_chk; $i++) {
		$k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;
		$expired = $_POST['expired'][$k];

		if (!$expired) $no_expired_index[] = $k;
	}

	if (count($no_expired_index) > 0) {
		alert();
	}

	for ($i = 0; $i < $post_count_chk; $i++) {
		// 실제 번호를 넘김
		$k = isset($_POST['chk'][$i]) ? (int) $_POST['chk'][$i] : 0;

		$wr_id = (int)$_POST['wr_id'][$k];
		$seq = (int)$_POST['chk_seq'][$k];
		$sid = (int)$_POST['sales2_id'][$k];
		$warehouse = (int)$_POST['warehouse'][$k];
		$stock = (int)$_POST['wr_37'][$k];
		$rack = $_POST['wr_rack'][$k];
		$expired = $_POST['expired'][$k];

		$rack_check_sql = "SELECT * FROM g5_rack WHERE gc_warehouse = {$warehouse} AND seq = {$rack}";
		$rack_check = sql_fetch($rack_check_sql);

		if (!$rack_check) {
			$fail_cnt++;
			continue;
		}

		if ($warehouse == '1000') {
			$filed = 'wr_32'; //한국
			$filed_real = 'wr_32_real';
			$warehouse_name = "한국창고";
		} else if ($warehouse == '3000') {
			$filed = 'wr_36'; //미국
			$filed_real = 'wr_36_real';
			$warehouse_name = "미국창고";
		}

		$chk = sql_fetch("select * from g5_temp_warehouse where tw_seq = '{$seq}'");

		if ($rack && $chk['wr_stock'] >= 1) {
			sql_trans_start();

			$sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$stock}',
			 				wr_product_id = '{$wr_id}',wr_sales3_id = '{$chk['sales2_id']}', wr_mb_id = '{$member['mb_id']}',
			  			wr_expired_date = '{$expired}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '임시창고 > 입고'";

			if (!sql_query($sql)) {
				sql_trans_rollback();
				$fail_cnt++;
				continue;
			}

			$sql2 = "update g5_write_product set {$filed} = {$filed} + {$stock}, {$filed_real} = {$filed_real} + {$stock},
			 wr_37 = wr_37 - {$stock}, wr_37_real = wr_37_real - {$stock} where wr_id = '{$wr_id}' limit 1";

			if (!sql_query($sql2)) {
				sql_trans_rollback();
				$fail_cnt++;
				continue;
			}

			$log = $member['mb_name'] . '(' . $member['mb_id'] . ') ' . G5_TIME_YMDHIS . ' ' . $warehouse_name . ' 로 ' . $stock . '개 재고이동\n';

			$sql3 = "update g5_temp_warehouse set wr_stock = wr_stock - {$stock}, wr_log = CONCAT(wr_log, '{$log}') where tw_seq = '{$seq}'";

			if (!sql_query($sql3)) {
				sql_trans_rollback();
				$fail_cnt++;
				continue;
			}

			if ($expired) {
				$data_query = "SELECT id, stock
										FROM g5_rack_expired
										WHERE warehouse = {$warehouse}
											AND rack_id = {$rack}
											AND product_id = {$wr_id}
											AND expired_date = '{$expired}'";

				$result = sql_fetch($data_query);

				if ($result) {
					$change_stock =  $result['stock'] + $stock;

					$sql4 = "UPDATE g5_rack_expired
							SET stock = {$change_stock}
							WHERE id = {$result['id']}";
				} else {
					$sql4 = "INSERT INTO g5_rack_expired
							SET warehouse = {$warehouse},
							rack_id = {$rack},
							product_id = {$wr_id},							
							stock = {$stock},
							expired_date = '{$expired}'";
				}				

				if (!sql_query($sql4)) {
					sql_trans_rollback();
					$fail_cnt++;
					continue;
				}
			}

			$suc_cnt++;
			sql_trans_commit();
		} else {
			$fail_cnt++;
		}
	}

	alert('일괄 재고이동이 완료되었습니다.\\n성공 : ' . $suc_cnt . '건\\n실패 : ' . $fail_cnt . '건');
} else if ($act_button === "일괄재고삭제") {
	if (count((array)$chk_seq) == 0) {
		alert("선택된 재고가 없습니다.");
		exit;
	}

	$total = 0;
	$succ = 0;
	$fail = 0;
	$error = 0;
	foreach ((array)$chk as $key => $val) {
		$chk_val = $chk_seq[$val];
		$sql = "SELECT A.*,B.seq FROM g5_temp_warehouse A \n";
		$sql .= "LEFT OUTER JOIN g5_sales2_list B ON B.seq=A.sales2_id \n";
		$sql .= "WHERE A.tw_seq='" . $chk_val . "'";
		$row = sql_fetch($sql);
		if ($row['seq']) {
			$fail++;
			continue;
		} else {
			$sql = "DELETE FROM g5_temp_warehouse WHERE tw_seq='" . $chk_val . "'";
			$rs = sql_query($sql);
			if ($rs) {
				$sql = "UPDATE g5_write_product SET wr_37= wr_37-" . $row['wr_stock'] . ",wr_37_real= wr_37_real-" . $row['wr_stock'] . " WHERE wr_id='" . $row['wr_product_id'] . "'";
				sql_query($sql);
				$succ++;
			} else {
				$error++;
			}
		}
		$total++;
	}

	$msg = "전체 : " . number_format($total) . "(성공 : " . number_format($succ) . " / 실패 : " . number_format($fail) . " / 오류 : " . number_format($error) . ")";
	alert($msg);
} else {
	alert('잘못 된 접근입니다.');
}
