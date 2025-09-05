<?php
include_once("./_common.php");

if (!$cust_code || !$seq) {
  echo json_encode(['status' => 'N']);
  exit;
}

$delivery_fee = max($wr_delivery_fee ?? 0, 0);
$delivery_fee2 = max($wr_delivery_fee2 ?? 0, 0);
$delivery_oil = max($wr_delivery_oil ?? 0, 0);

$sql = "select * from g5_sales3_list where seq = '$seq'";
$sales3 = sql_fetch($sql);


if (!$sales3) {
  echo json_encode(['status' => 'N']);
  exit;
}

if ($service_type == '0003') {
  $delivery_fee_total = $delivery_fee + $delivery_fee2;
  $sql = "update g5_sales3_list set wr_delivery = '$cust_code', wr_delivery_fee = '0', wr_delivery_fee2 = '{$delivery_fee_total}', wr_delivery_oil = '{$delivery_oil}' where seq = '$seq' limit 1";
} else {
  $sql = "update g5_sales3_list set wr_delivery = '$cust_code', wr_delivery_fee = '{$delivery_fee}', wr_delivery_fee2 = '{$delivery_fee2}', wr_delivery_oil = '{$delivery_oil}' where seq = '$seq' limit 1";
}

sql_query($sql);

echo json_encode(['status' => 'Y']);
exit;
