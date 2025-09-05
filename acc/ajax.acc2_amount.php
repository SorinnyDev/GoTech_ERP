<?php
include_once('./_common.php');

if (!$wr_orderer) {
  echo json_encode(['status' => 'N']);
  exit;
}

if (!$base_date) {
  echo json_encode(['status' => 'N']);
  exit;
}

if (!$amount) {
  $amount = 0;
}

$wr_orderer = explode("|", $wr_orderer);
$wr_orderer = $wr_orderer[1];

$sql = "insert into g5_acc2_carryover_amount set wr_orderer = '$wr_orderer', base_date = '$base_date', amount = '$amount'
on duplicate KEY UPDATE amount = '$amount'";

sql_query($sql);

echo json_encode(['status' => 'Y']);
exit;