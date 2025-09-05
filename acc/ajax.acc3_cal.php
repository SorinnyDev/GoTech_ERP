<?php 
  include_once('./_common.php');

  if (!isDefined($type) || !isDefined($value) || !isDefined($is_checked) || !isDefined($seq)) {
    exit;
  }

  if (!$is_checked) {
    $value = 0;
  }


  $sql = "
  INSERT INTO g5_sales3_cal
  SET
      sales3_seq = {$seq},
      {$type} = $value,
      {$type}_status = $is_checked
  ON DUPLICATE KEY UPDATE
      {$type} = VALUES($type),
      {$type}_status = VALUES({$type}_status)
  ";		


  $result = sql_query($sql);

  echo json_encode(['status' => $result]);