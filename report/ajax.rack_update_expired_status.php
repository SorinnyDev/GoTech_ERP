<?php
include_once './_common.php';

if (!isset($seq) || isEmpty($seq) || !isset($product_id) || isEmpty($product_id)) {
  die('잘못된 접근입니다.');
}

if (!isset($expired_date) || isEmpty($expired_date)) {
  die('유통기한 날짜를 입력해주세요.');
}

$sql = "select r.seq as 'rack_id', rep.product_id from g5_rack as r left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = '$product_id' where seq = '$seq'";
$rack = sql_fetch($sql);

if (isEmpty($rack)) {
  die('존재하지 않는 랙 입니다.');
}

$status = !$rack['product_id'];

if ($status) {
  $sql = "insert into g5_rack_expired set rack_id = '{$rack['rack_id']}', product_id = '{$product_id}', expired_date = '{$expired_date}'";
  sql_query($sql);
} else {
  $sql = "delete from g5_rack_expired where rack_id = '{$rack['rack_id']}' and product_id = '$product_id'";
  sql_query($sql);
}

die('Y');

