<?php
include_once('./_common.php');

if (!$seq || !$pay_date) {
  response('N', '잘못된 접근입니다.');
}

$query = "
select seq, IFNULL(wr_warehouse_price, 0) as pay_price, wr_misu
from g5_sales2_list as sl
where seq = '$seq'
";

$result = sql_fetch($query);

if (!$result) {
  response('N', '주문 정보를 찾지 못했습니다.');
}

$pay_price = $result['pay_price'];
$misu = $result['wr_misu'];

if ($misu == 0) {
  response('N', '이미 지급완료된 주문입니다.');
}

$query = "
select * from g5_sales2_after_pay
where sales2_id = '$seq'
";

$sales2_after_pay = sql_fetch($query);

if ($sales2_after_pay) {
  $query = "
  update g5_sales2_after_pay
  set pay_price = '$misu', wr_misu = '$pay_price', pay_date = '$pay_date'
  where id = '{$sales2_after_pay['id']}'
  ";

} else {
  $query = "
  insert into g5_sales2_after_pay set sales2_id = '$seq', pay_price = '$misu', wr_misu = '$pay_price', pay_date = '$pay_date',reg_datetime = now()
  ";
}

$result = sql_query($query);

if (!$result) {
  response('N', '서버 오류입니다.');
}

response('Y', '지정 지급 처리되었습니다.');

function response($status, $message = '')
{
  echo json_encode(['status' => $status, 'message' => $message]);
  exit;
}