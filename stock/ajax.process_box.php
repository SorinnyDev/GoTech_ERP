<?php
include_once('./_common.php');

if ($is_guest) die('n');

if (!$id) {
  response('잘못된 접근입니다.');
}

$query = "update g5_stock_box set is_transfer = true where id = '$id'";
sql_query($query);

response('이관되었습니다.');

function response($result)
{
  echo json_encode(['result' => $result]);
  exit;
}
