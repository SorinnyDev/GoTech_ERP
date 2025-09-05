<?php
include_once("./_common.php");

if(!$seq || !$warehouse)
    die('n');


$sql = "update g5_sales2_list set wr_warehouse = '{$warehouse}' where seq = '{$seq}'";

if(sql_query($sql)){
	die('y');
} else {
	die('n');
}