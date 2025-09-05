<?php
include_once('./_common.php');

if ($is_guest) die('n');

if (!$box_code) {
  response(false);
}

$query = "update g5_stock_box set is_deleted = true where box_code = '{$box_code}'";
$result = sql_query($query);

$query = "update g5_stock_box_order set is_deleted = true where box_code = '{$box_code}'";
$result = sql_query($query);

response($result);

function response($result)
{
  echo json_encode(['result' => $result]);
  exit;
}

