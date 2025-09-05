<?php 
include_once('./_common.php');

if(!$wr_id) die('n');

$wr_id = (int)$_POST['wr_id'];
$seq = (int)$_POST['seq'];
$sid = (int)$_POST['sid'];
$warehouse = (int)$_POST['warehouse'];
$stock = (int)$_POST['stock'];
$rack = $_POST['rack'];

if(!$warehouse) die('nn');

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


/* 24.05.30 업데이트를 위해 
if($chk['wr_state'] == 0 && !$rack) {
	
	//처리하지 않고 랙을 선택하지 않았을때 한국/미국창고 입고자료가져오기로 이동.
	$sql2 = "update g5_temp_warehouse set wr_stock2 = '{$stock}', wr_state = 1, wr_state_mbid = '{$member['mb_id']}', wr_state_date = '".G5_TIME_YMDHIS."', wr_rack = '{$rack}' where tw_seq = '{$seq}'";
	sql_query($sql2, true);
	
	$rack_info = sql_fetch("select seq from g5_rack where gc_warehouse = '{$warehouse}' and gc_name ='임시창고'");
	
	//24.05.12 post[sid] 에서 chk[sales2_id]로 변경
	$sql3 = "update g5_sales2_list set wr_warehouse = '{$warehouse}', wr_rack = '{$rack_info['seq']}' where seq = '{$chk['sales2_id']}' ";
	sql_query($sql3, true);
	
	
	//재고이관 기록 240517
	$sql4 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack_info['seq']}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql4, true);
} */

if($rack && $chk['wr_stock'] >= 1) {
	//랙 선택시 
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$stock}',wr_sales3_id = '{$chk['sales2_id']}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '임시창고 > 입고'";
	if(!sql_query($sql)) die('n');
	
	//한국/미국 창고 재고 증감 및 임시창고 차감
	$sql2 = "update g5_write_product set {$filed} = {$filed} + {$stock}, {$filed_real} = {$filed_real} + {$stock}, wr_37 = wr_37 - {$stock}, wr_37_real = wr_37_real - {$stock} where wr_id = '{$wr_id}' limit 1";
	if(!sql_query($sql2)) die('n');
	
	//로그 기록
	$log = $member['mb_name'].'('.$member['mb_id'].') '.G5_TIME_YMDHIS.' '.$warehouse_name.' 로 '.$stock.'개 재고이동\n';
	
	$sql3 = "update g5_temp_warehouse set wr_stock = wr_stock - {$stock}, wr_log = CONCAT(wr_log, '{$log}') where tw_seq = '{$seq}'";
	
	if(!sql_query($sql3)) die('n');
	
	
} else {
	die('n');
}

die('y');