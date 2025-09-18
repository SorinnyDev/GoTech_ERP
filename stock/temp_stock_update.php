<?php
include_once('./_common.php');

if (!$wr_id) die('n');

$wr_id = (int)$_POST['wr_id'];
$seq = (int)$_POST['seq'];
$sid = (int)$_POST['sid'];
$warehouse = (int)$_POST['warehouse'];
$stock = (int)$_POST['stock'];
$rack = $_POST['rack'];
$expired = $_POST['expired'];
$rack_seq = (int)$_POST['rack_seq'];

if (!$warehouse) die('nn');

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
	//랙 선택시 
    //랙 선택시
    $sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack_seq}', 
	wr_stock = '{$stock}',wr_sales3_id = '{$chk['sales2_id']}', wr_product_id = '{$wr_id}', 
	wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '임시창고 > 입고'";
	if (!sql_query($sql)) {
		sql_trans_rollback();
		die('n1');
	}

	//한국/미국 창고 재고 증감 및 임시창고 차감
    $sql2 = "update g5_write_product set {$filed} = {$filed} + {$stock}, {$filed_real} = {$filed_real} + {$stock}, wr_37 = wr_37 - {$stock}, wr_37_real = wr_37_real - {$stock} where wr_id = '{$wr_id}' limit 1";
	if (!sql_query($sql2)) {
		sql_trans_rollback();
		die('n2');
	}

	//로그 기록
	$log = $member['mb_name'] . '(' . $member['mb_id'] . ') ' . G5_TIME_YMDHIS . ' ' . $warehouse_name . ' 로 ' . $stock . '개 재고이동\n';

    $sql3 = "update g5_temp_warehouse set wr_stock = wr_stock - {$stock}, wr_log = CONCAT(wr_log, '{$log}') where tw_seq = '{$seq}'";

	if (!sql_query($sql3)) {
		sql_trans_rollback();
		die('n3');
	}

	if ($expired) {

        $sql = "select r.seq as 'rack_id', rep.product_id from g5_rack as r 
                left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = '$wr_id' 
                where seq = '$rack_seq'";
        $rack = sql_fetch($sql);

        if (isEmpty($rack)) {
            die('n6');
        }

        $status = !$rack['product_id'];

        if ($status) {
            $sql = "insert into g5_rack_expired set rack_id = '{$rack['rack_id']}', product_id = '{$wr_id}', expired_date = '{$expired}'";
            sql_query($sql);
        }

	}
} else {
	die('n5');
}

sql_trans_commit();
die('y');
