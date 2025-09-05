<?php
include_once('../common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

$arr = [];
$result = false;

$add_sql = " wr_warehouse = '{$wr_warehouse}', 
             wr_rack = '{$wr_rack}', 
             wr_stock = '{$wr_stock}', 
             wr_product_id = '{$wr_product_id}', 
             wr_mb_id = '{$member['mb_id']}' ";

$chk = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_stock_rack_research WHERE wr_mb_id = '{$member['mb_id']}'
                                                                      AND   wr_warehouse = '{$wr_warehouse}'
                                                                      AND   wr_rack = '{$wr_rack}'
                                                                      AND   wr_product_id = '{$wr_product_id}' ");

if($chk['cnt'] > 0){
    $sql = " UPDATE g5_stock_rack_research SET   wr_stock = '{$wr_stock}', 
                                                 up_datetime = '".G5_TIME_YMDHIS."' 
                                           WHERE wr_mb_id = '{$member['mb_id']}'
                                           AND   wr_warehouse = '{$wr_warehouse}'
                                           AND   wr_rack = '{$wr_rack}'
                                           AND   wr_product_id = '{$wr_product_id}' ";
}else{
    $sql = " INSERT INTO g5_stock_rack_research SET {$add_sql}, wr_datetime = '".G5_TIME_YMDHIS."' ";
}

if(sql_query($sql)){
    $result = true;
}else{
    $result = false;
}
	
$arr['result'] = $result;
$arr['total'] = sql_fetch("SELECT SUM(wr_stock) AS total FROM g5_stock_rack_research WHERE wr_warehouse = '{$wr_warehouse}' ")['total'];
echo json_encode($arr);
?>