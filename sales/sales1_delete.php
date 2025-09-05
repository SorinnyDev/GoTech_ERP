<?php
include_once('./_common.php');

if(!$_POST['seq']) die('n');

$obj = sql_fetch("select wr_order_num from g5_sales0_list where seq = '{$seq}'");

sql_query("delete from g5_write_sales where wr_subject = '{$obj['wr_order_num']}' ");
sql_query("delete from g5_sales0_list where wr_order_num = '{$obj['wr_order_num']}' ");

sql_query("UPDATE g5_board SET bo_count_write = bo_count_write - 1 WHERE bo_table = 'sales' ");

die('y');
