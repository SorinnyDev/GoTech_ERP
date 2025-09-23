<?php 
include_once('./_common.php');

if(!$seq) die('n');

$seq = (int)$_POST['seq'];
$wr_rack = (int)$_POST['wr_rack'];
$expired_date = $_POST['expired_date'];

$row = sql_fetch("select * from g5_stock_move where seq = '{$seq}'");

if(!$row) die('nn');

$sql = "update g5_stock_move set 
wr_state_date = '".G5_TIME_YMDHIS."',
wr_state = 1,
mb_id2 = '{$member['mb_id']}',
wr_rack = '{$wr_rack}'
where seq = '{$row['seq']}'
";

if(sql_query($sql, true)) {
	
	//입력됐다면 재고증감 

	$wr_move_log = $storage_arr[$row['wr_out_warehouse']]['code_nm'].">".$storage_arr[$row['wr_in_warehouse']]['code_nm']." 재고이관 완료";
	
	$field = $storage_arr[$row['wr_in_warehouse']]['field'];
	$field_real = $storage_arr[$row['wr_in_warehouse']]['field_real'];

	$sql = "update g5_write_product set {$field} = {$field} + {$row['wr_stock']},{$field_real} = {$field_real} + {$row['wr_stock']} where wr_id = '{$row['product_id']}'";
	$result = sql_query($sql);
	if($result){
	
		//랙 재고 입력 
		$sql = "insert into g5_rack_stock set wr_warehouse = '{$row['wr_in_warehouse']}', wr_rack = '{$wr_rack}', wr_stock = '{$row['wr_stock']}', wr_product_id = '{$row['product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '{$wr_move_log}'";
		$result = sql_query($sql);

        if (!empty($expired_date)) {
            $sql_expired = "INSERT INTO g5_rack_expired SET rack_id = '{$wr_rack}', product_id = '{$row['product_id']}', expired_date = '{$expired_date}'";
            sql_query($sql_expired);
        }

		if($result){
			echo json_encode(array("ret_code"=>true,"message"=>"이관 처리가 완료되었습니다."));
			exit;
		}else{
			echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[재고 로그 등록]"));
			exit;
		}
	}else{
		echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[재고 수정]"));
		exit;
	}
	

} else {
	echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[재고 등록]"));
	exit;
}