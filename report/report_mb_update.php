<?php 
include_once('../common.php');

if(!$wr_id) die('n');

$member['mb_id'] = "0023";

$idx = $_POST['idx'];
$wr_id = $_POST['wr_id'];
$wr_32 = (int)$_POST['wr_32'];
$wr_36 = (int)$_POST['wr_36'];
$wr_37 = (int)$_POST['wr_37'];
$wr_42 = (int)$_POST['wr_42'];
$wr_43 = (int)$_POST['wr_43'];
$wr_44 = (int)$_POST['wr_44'];

$chk = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_stock_research WHERE mb_id = '{$member['mb_id']}' AND wr_id = '{$wr_id}' ");

$add_sql = "    wr_32 = {$wr_32}, 
                wr_36 = {$wr_36}, 
                wr_37 = {$wr_37}, 
                wr_42 = {$wr_42}, 
                wr_43 = {$wr_43}, 
                wr_44 = {$wr_44} ";
           
if($chk['cnt'] > 0){
    $sql = " UPDATE g5_stock_research SET {$add_sql}, up_datetime = now() WHERE idx = '{$idx}'";
}else{
    $sql = " INSERT INTO g5_stock_research SET {$add_sql}, mb_id = '{$member['mb_id']}', wr_id = '{$wr_id}', wr_datetime = now() ";
}

if(sql_query($sql)) {
	$result = true;
} else {
	$result = false;
}

$obj['result'] = $result;
$obj['total'] = sql_fetch("SELECT SUM(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44) as total FROM g5_stock_research WHERE wr_id = '{$wr_id}' ")['total'];

echo json_encode($obj);

?>