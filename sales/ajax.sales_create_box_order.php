<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

if (!$seq || !$box_code) alert_close('잘못된 접근입니다.');

$query = "select * from g5_sales3_list where seq = '$seq'";

$sales3 = sql_fetch($query);

if (!$sales3) {
  alert_close('잘못된 접근입니다.');
}

$query = "
  select * from g5_stock_box_order where wr_order_num = '{$sales3['wr_order_num']}'
";

$box_order = sql_fetch($query);

if (count($box_order) > 0) {
  $query = "delete from g5_stock_box_order where id = '{$box_order['id']}'";
  sql_query($query);
}

$query = "
  insert into g5_stock_box_order set box_code = '$box_code', wr_order_num = '{$sales3['wr_order_num']}', reg_datetime = now()
";

sql_query($query);

alert_close('박스로 이관되었습니다.');