<?php 
include_once('./_common.php');

alert('실행되지 않았습니다.');
exit;

$sql = "TRUNCATE TABLE g5_sales{$table_name}_list";

if(sql_query($sql)) {
	
	if($table_name == 2) {
		@sql_query("TRUNCATE TABLE g5_temp_warehouse");
	}
	
	alert('데이터가 초기화 되었습니다.');
} else { 
	alert('처리 중 오류가 발생했습니다.');
}