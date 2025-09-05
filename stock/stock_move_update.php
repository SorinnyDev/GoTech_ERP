<?php 
include_once('./_common.php');

if(!$wr_id) die('n');

$wr_id = (int)$_POST['wr_id'];
$stock = (int)$_POST['stock'];
$out = $_POST['wr_out_warehouse']; // 이관창고(출발)
$in = $_POST['wr_in_warehouse']; // 이관창고(도착)

$out_arr = explode("|",$out);
$wr_out_warehouse = $out_arr[0];

$in_arr = explode("|",$in);
$wr_in_warehouse = $in_arr[0];

$field = $storage_arr[$wr_out_warehouse]['field'];
$field_real = $storage_arr[$wr_out_warehouse]['field_real'];

# 해당 필드의 이관 가능 재고 체크( 출고 예정 재고로 체크 )
$sql = "SELECT {$field},{$field_real} FROM g5_write_product WHERE wr_id='{$wr_id}'";
$row = sql_fetch($sql);
if($row[$field] < $stock){
	echo json_encode(array("ret_code"=>false,"message"=>"이관 가능 재고가 부족합니다."));
	exit;
}

$sql = "insert into g5_stock_move set 
mb_id = '{$member['mb_id']}',
product_id = '{$wr_id}',
wr_out_warehouse = '{$wr_out_warehouse}',
wr_in_warehouse = '{$wr_in_warehouse}',
wr_rack_out = '{$wr_rack_out}',
wr_stock = '{$stock}',
wr_datetime = '".G5_TIME_YMDHIS."',
wr_state = 0
";

if(sql_query($sql)) {
	
	//입력됐다면 재고차감 
	$wr_move_log = $storage_arr[$wr_out_warehouse]['code_nm'].">".$storage_arr[$wr_in_warehouse]['code_nm']." 재고이관";
	
	$sql = "update g5_write_product set {$field} =  {$field} - {$stock}, {$field_real} = {$field_real} - {$stock} where wr_id = '{$wr_id}'";
	$result = sql_query($sql);
	
	if($result){
	
		$sql = "insert into g5_rack_stock set wr_warehouse = '{$wr_out_warehouse}', wr_rack = '{$wr_rack_out}', wr_stock = '-{$stock}', wr_product_id = '{$wr_id}', wr_sales3_id = '',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '{$wr_move_log}'";
		$result = sql_query($sql);
		if($result){
			echo json_encode(array("ret_code"=>true,"message"=>"재고 이관 등록이 완료되었습니다."));
			exit;
		}else{
			echo json_encode(array("ret_code"=>false,"message"=>"데이터 업로드에 실패하였습니다.[".$storage_arr[$wr_out_warehouse]['code_nm']." 차감]"));
			exit;
		}
	}else{
		echo json_encodeE(array("ret_code"=>false,"message"=>"데이터 업로드에 실패하였습니다.[재고 수정]"));
		exit;
	}
	
	
} else {
	echo json_encode(array("ret_code" => false,"message"=>"데이터 업로드에 실패하였습니다.[재고이관 등록]"));
	exit;
}