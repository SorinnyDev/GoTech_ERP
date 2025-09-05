<?php 
include_once('./common.php');

codeView("테스트 페이지 입니다.");
if(80 >= 89) {
			echo "d";
		} else {
			echo "Y";
		}
/*
$query = "SELECT *, COUNT(*) AS cnt FROM g5_write_sales GROUP BY wr_subject HAVING cnt > 1";
$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
	
	
	$ordernum = $row['wr_subject'];
	$cnt = $row['cnt'];
	$wr_2 = $row['wr_2'];

	for ($i = 1; $i <= $cnt; $i++) {
		// 중복 주문번호 뒤에 문자열 추가 및 업데이트
		$newOrderNumber = $row['wr_subject'] . chr(64 + $i); // A, B, C, ...
		$updateQuery = "UPDATE g5_write_sales SET wr_subject = '$newOrderNumber',
		 wr_1 = '{$row['wr_1']}',
		 wr_2 = '{$row['wr_2']}',
		 wr_3 = '".addslashes($row['wr_3'])."',
		 wr_4 = '{$row['wr_4']}',
		 wr_5 = '".addslashes($row['wr_5'])."',
		 wr_6 = '{$row['wr_6']}',
		 wr_7 = '{$row['wr_7']}',
		 wr_8 = '{$row['wr_8']}',
		 wr_9 = '".addslashes($row['wr_9'])."',
		 wr_10 = '".addslashes($row['wr_10'])."',
		wr_15 = '".addslashes($row['wr_15'])."',
		wr_18 = '".addslashes($row['wr_18'])."',
		wr_19 = '".addslashes($row['wr_19'])."',
		wr_20 = '".addslashes($row['wr_20'])."',
		wr_21 = '".addslashes($row['wr_21'])."',
		wr_22 = '".addslashes($row['wr_22'])."',
		wr_23 = '".addslashes($row['wr_23'])."',
		wr_24 = '".addslashes($row['wr_24'])."',
		wr_25 = '1'
		WHERE wr_subject = '$ordernum' LIMIT 1";
		
		echo $updateQuery;
		
		
		sql_query($updateQuery, true);
	}
}
/*
$sql = "select * from test";
$rst = sql_query($sql);
for($i=0; $row=sql_fetch_array($rst); $i++) {

$sql = "insert into g5_member set 
mb_id = '{$row['id']}',
mb_name = '{$row['name']}',
mb_nick = '{$row['name']}',
mb_level = 10,
mb_password = '".get_encrypt_string('1234')."'
";

sql_query($sql);

}*/

?>