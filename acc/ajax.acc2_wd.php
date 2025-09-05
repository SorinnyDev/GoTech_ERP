<?php
include_once('./_common.php');


if (!$wd_date || !$wd_orderer) {
  response('N', '잘못된 접근입니다.');
}

$query = "
insert into g5_acc2_wd 
set wd_date = '$wd_date', 
wd_orderer = '$wd_orderer', 
wd_price = '$wd_price', 
wd_text = '$wd_text', 
wd_reg_datetime = now()
";

sql_query($query);

response('Y', '저장되었습니다.');


function response($status, $message = '')
{
  echo json_encode(['status' => $status, 'message' => $message]);
  exit;
}