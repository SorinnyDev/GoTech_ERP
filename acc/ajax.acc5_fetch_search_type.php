<?php
include_once './_common.php';

if (empty($ordernum)) {
  alert('잘못된 접근입니다.');
}

$sql = "select * from g5_sales3_list where wr_ori_order_num like '$ordernum'";
$list = sql_fetch($sql);

echo $list['wr_domain'];