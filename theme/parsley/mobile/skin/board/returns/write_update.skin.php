<?php
include_once("../../_common.php");

$sql = "update {$write_table} set 
mb_id = '{$mb_id}',
wr_name = '{$wr_name}',
wr_subject = '{$wr_subject}',
wr_date1 = '{$wr_date1}',
wr_date2 = '{$wr_date2}',
wr_1 = '{$wr_1}',
wr_2 = '{$wr_2}',
wr_3 = '{$wr_3}'
where wr_id = '{$wr_id}'";
sql_query($sql, true);