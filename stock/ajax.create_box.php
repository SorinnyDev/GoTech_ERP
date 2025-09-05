<?php
include_once('./_common.php');

if ($is_guest) die('n');

$box_code = generateRandomCode(6);

$query = "insert into g5_stock_box set box_code = '$box_code', reg_datetime = now()";
$result = sql_query($query);

response($result);

function response($result)
{
  echo json_encode(['result' => $result]);
  exit;
}

function generateRandomCode($length = 6) {
  $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $code = '';
  $maxIndex = strlen($characters) - 1;

  for ($i = 0; $i < $length; $i++) {
    $code .= $characters[random_int(0, $maxIndex)];
  }

  return $code;
}
