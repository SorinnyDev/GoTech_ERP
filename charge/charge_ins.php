<?
include_once('./_common.php');

$arr = array();
$arr['USD'] = trim("1,347.00");
$arr['JPY'] = trim("936.05");
$arr['EUR'] = trim("1,504.10");
$arr['SGD'] = trim("1,043.96");
$arr['VND'] = trim("5.47");
$arr['GBP'] = trim("1,794.58");
$arr['MXN'] = trim("69.43");
$arr['BRL'] = trim("239.19");
$arr['CAD'] = trim("993.17");

$sql = "SELECT * FROM g5_code_list WHERE code_type='6' AND del_yn = 'N' AND code_use='Y'";
$rs = sql_query($sql);
while($row = sql_fetch_array($rs)){
	$data = $arr[$row['code_value']];
	if($data != ""){
		$sql = "INSERT INTO g5_excharge_202409(ex_date,ex_kor,ex_eng,rate,mb_id,wr_datetime)";
		$sql .= "VALUES('2024-09-22','".$row['code_name']."','".$row['code_value']."','".$data."','auto',NOW());";
		codeView($sql);
	}
}

?>