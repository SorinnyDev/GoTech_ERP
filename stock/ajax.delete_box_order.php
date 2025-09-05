<?php
include_once('./_common.php');

if ($is_guest) die('n');

if (!$seq) {
  response(false);
}

$query = "select * from g5_sales3_list where seq = '$seq'";
$sales3 = sql_fetch($query);

$query = "update g5_stock_box_order set is_deleted = true where wr_order_num = '{$sales3['wr_order_num']}'";
$result = sql_query($query);

response($result);

function response($result)
{
  echo json_encode(['result' => $result]);
  exit;
}

