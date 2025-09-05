<?php
include_once('./_common.php');

if (!$sc_domain) {
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

$sql = "insert into g5_acc3_carryover_amount set sc_domain = '$sc_domain', base_date = '$base_date', amount = '$amount'
on duplicate KEY UPDATE amount = '$amount'";

sql_query($sql);

echo json_encode(['status' => 'Y']);
exit;