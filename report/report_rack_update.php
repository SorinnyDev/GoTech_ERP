<?php
include_once('../common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

$add_sql = " wr_warehouse = '{$warehouse}', 
             wr_rack = '{$seq}', 
             wr_stock = '{$ea}', 
             wr_product_id = '{$wr_id}', 
             wr_mb_id = '{$member['mb_id']}' ";

$chk = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_stock_rack_research WHERE  ");

if($chk['cnt'] > 0){
    $sql = " UPDATE g5_stock_rack_research SET {$add_sql}, up_datetime = '".G5_TIME_YMDHIS."' ";
}else{
    $sql = " INSERT INTO g5_stock_rack_research SET {$add_sql}, wr_datetime = '".G5_TIME_YMDHIS."' ";
}

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}
	

?>