<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

if($w == 'd') {
	sql_query("delete from g5_rack_stock where seq = '{$seq}'");
	
	alert('재고 기록이 삭제되었습니다.');
}