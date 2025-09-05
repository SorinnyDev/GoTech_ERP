<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


$sql = "update {$write_table} set 
wr_subject = '{$wr_subject}',
mb_id = '{$mb_id}',
wr_name = '{$wr_name}',
wr_2 = '{$wr_2}',
wr_3 = '{$wr_3}',
wr_5 = '{$wr_5}',
wr_7 = '{$wr_7}',
wr_11 = '{$wr_11}',
wr_12 = '{$wr_12}',
wr_13 = '{$wr_13}',
wr_14 = '{$wr_14}',
wr_15 = '{$wr_15}',
wr_16 = '{$wr_16}',
wr_17 = '{$wr_17}',
wr_18 = '{$wr_18}',
wr_19 = '{$wr_19}',
wr_20 = '{$wr_20}',
wr_21 = '{$wr_21}',
wr_22 = '{$wr_22}',
wr_23 = '{$wr_23}',
wr_24 = '{$wr_24}',
wr_25 = '{$wr_25}',
wr_26 = '{$wr_26}'

where wr_id = '{$wr_id}'";

sql_query($sql,true);