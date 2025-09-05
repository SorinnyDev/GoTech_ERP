<?php
include_once('./_common.php');
header("Content-Type: application/json");

$row = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}' and wr_delYn = 'N'");

if(!$row) die('n');

echo json_encode($row);