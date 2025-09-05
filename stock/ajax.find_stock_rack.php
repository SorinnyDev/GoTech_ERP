<?php
include_once './_common.php';

if (empty($warehouse)) {
  json_response(false, '잘못된 접근입니다.');
}

//랙별 열 번호
$pattern = '/[A-Za-z]\d*/';
$rack = array();

$sql_where = " and gc_warehouse = '{$warehouse}'";

$sql = "select * from g5_rack where (1) {$sql_where}";
$rst = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($rst); $i++) {

  $result = preg_replace($pattern, '', $row['gc_name']);

  if (!$result) $result = "기타";
  $rack[$result] .= $row['seq'] . "|";
}

ksort($rack);

json_response(true, '', $rack);
