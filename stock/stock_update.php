<?php 
include_once('./_common.php');

if(!$wr_id) die('n');

$wr_id = (int)$_POST['wr_id'];
$stock = (int)$_POST['stock'];

$sql = "update g5_write_product set wr_32 = {$stock1}, wr_36 = {$stock2}, wr_37 = {$stock3} where wr_id = '{$wr_id}'";

if(sql_query($sql)) {
	die('y');
} else {
	die('n');
}