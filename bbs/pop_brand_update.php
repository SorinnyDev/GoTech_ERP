<?php
include_once('./_common.php');


if(!$_POST['wr_23']) alert('잘못 된 접근입니다.');

$mb = get_member($wmb_id, 'mb_name');

$code = sql_fetch("select code_name from g5_code_list where idx = '{$wr_23}'");


sql_query("update g5_write_product set mb_id = '{$wmb_id}', wr_name = '{$mb['mb_name']}' where wr_23 = '{$wr_23}'");



alert('['.$code['code_name'].'] 브랜드의 담당자를 ['.$mb['mb_name'].'('.$wmb_id.')]으로 일괄 변경되었습니다.');