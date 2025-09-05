<?php
include_once('./_common.php');


if (!$id) {
  response('N', '잘못된 접근입니다.');
}

$query = "
delete from g5_acc2_wd where id = '$id' limit 1
";

sql_query($query);

response('Y', '삭제되었습니다.');


function response($status, $message = '')
{
  echo json_encode(['status' => $status, 'message' => $message]);
  exit;
}