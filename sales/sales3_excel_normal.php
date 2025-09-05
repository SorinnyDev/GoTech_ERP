<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$filename = "release_list_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	<tr>
		<td colspan=\"11\"><strong style=\"font-size:16px\">출고등록(한국)</strong><br> 출력일시 : ".G5_TIME_YMDHIS."</td>
	</tr>
    <tr style=\"background:#f2f2f2\">
		<th style=\"width:50px;height:17pt;\">순번</th>
		<th style=\"width:100px;height:17pt;\">도메인명</th>
		<th style=\"width:200px;height:17pt;\">주문번호</th>
		<th style=\"width:250px;height:17pt;\">배송지 주소1</th>
		<th style=\"width:200px;height:17pt;\">배송지 주소2</th>
		<th style=\"width:150px;height:17pt;\">상품코드</th>
		<th style=\"width:500px;height:17pt;\">상품명칭</th>
		<th style=\"width:70px;height:17pt;\">수량</th>
	
		<th style=\"width:100px\">랙번호</th>
    </tr>
";

if($warehouse == 1000)
	$sql_search = " (wr_warehouse = 1000 or wr_warehouse = 9000) ";
else if($warehouse == 3000)
	$sql_search = " wr_warehouse = 3000 ";

if($date1 && $date2)
	$sql_search .= " and wr_date4 BETWEEN '{$date1}' AND '{$date2}'";

$sql_search .= " and seq IN (".implode(',', $_POST['seq']).") ";

if(!$sst && !$sod) {
	$sst = "seq";
	$sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "select * from g5_sales3_list where {$sql_search} {$sql_order} ";
$rst = sql_query($sql);
for ($i=0; $row=sql_fetch_array($rst); $i++) {

$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}'");

$release_state = "X";
if($row['wr_release_use'] == 1) {
	$release_state = 'O';
}

$rack = sql_fetch("select wr_rack from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$row['wr_product_id']}' GROUP BY wr_rack HAVING SUM(wr_stock) > 0;");

$rack_name = get_rack_name($row['wr_rack']);

$EXCEL_FILE .= "
    <tr>
		<td style=\"text-align:center;height:17pt;\">".($i+1)."</td>
		<td>".$row['wr_domain']."</td>
		<td>".$row['wr_order_num']."</td>
		<td>".$row['wr_deli_addr1']."</td>
		<td>".$row['wr_deli_addr2']."</td>
		<td>".$row['wr_code']."</td>
		<td>".$item['wr_subject']."</td>
		<td>".$row['wr_ea']."</td>
		<td>".$rack_name."</td>
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;