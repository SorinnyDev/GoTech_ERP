<?php 
include_once('./_common.php');

if(!$wr_id) die('n');

$wr_id = (int)$_POST['wr_id'];
$seq = (int)$_POST['seq'];
$sid = (int)$_POST['sid'];
$warehouse = (int)$_POST['warehouse'];
$stock = (int)$_POST['stock'];
$rack = $_POST['rack'];

if($warehouse == '1000')
	$filed = 'wr_32'; //한국
else if($warehouse == '3000')
	$filed = 'wr_36'; //미국

//한국/미국 창고 재고 증감 및 임시창고 차감
$sql = "update g5_write_product set {$filed} = {$filed} + {$stock}, wr_37 = wr_37 - {$stock} where wr_id = '{$wr_id}' limit 1";


$chk = sql_fetch("select * from g5_temp_warehouse where tw_seq = '{$seq}'");

if($chk['wr_state'] == 0 && !$rack) {
	
	//처리하지 않고 랙을 선택하지 않았을때 한국/미국창고 입고자료가져오기로 이동.
	$sql2 = "update g5_temp_warehouse set wr_stock2 = '{$stock}', wr_state = 1, wr_state_mbid = '{$member['mb_id']}', wr_state_date = '".G5_TIME_YMDHIS."', wr_rack = '{$rack}' where tw_seq = '{$seq}'";
		sql_query($sql2, true);

	$sql3 = "update g5_sales2_list set wr_warehouse = '{$warehouse}' where seq = '{$sid}' ";
	sql_query($sql3);
} 

if($rack) {
	//랙 선택시 
	$sql4 = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql4);
}
if(sql_query($sql)) {
	
	die('y');
} else {
	die('n');
}