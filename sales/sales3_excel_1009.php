<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');
ini_set('display_errors', 1);
error_reporting(E_ALL);
$filename = "SHIPTER_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	
    <tr style=\"background:#ddd\">
		<th style=\"width:70px\">도착국가(국가코드)</th>
		<th style=\"width:100px\">배송타입</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:140px\">주문번호</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:140px\">발송자</th>
		<th style=\"width:140px\">발송자 전화번호</th>
		<th style=\"width:250px\">발송자 주소</th>
		<th style=\"width:100px\">수취인명</th>
		<th style=\"width:100px\">수취인 전화번호</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:200px\">수취인 우편번호</th>
		<th style=\"width:100px\">수취인 지역명</th>
		<th style=\"width:100px\">수취인 도시명</th>
		<th style=\"width:70px\">수취인 상세주소</th>
		<th style=\"width:100px\">CPF/VOEC 넘버</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:70px\"></th>
		<th style=\"width:70px\"></th>
		<th style=\"width:100px\">무게(kg)</th>
		<th style=\"width:100px\">박스갯수</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\">통화단위</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\">상품명</th>
		<th style=\"width:100px\">수량</th>
		<th style=\"width:100px\">단가</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\">HS CODE</th>
		<th style=\"width:100px\">배송비</th>
		<th style=\"width:100px\">추가 배송비</th>
		<th style=\"width:100px\">상품종류</th>
		<th style=\"width:100px\"></th>
		<th style=\"width:100px\">IOSS 넘버</th>
		
    </tr>
";

if($warehouse == 1000)
	$sql_search = " (wr_warehouse = 1000 or wr_warehouse = 9000) ";
else if($warehouse == 3000)
	$sql_search = " wr_warehouse = 3000 ";


$sql_order = "order by seq desc";

$sql_search .= " and seq IN (".implode(',', $_POST['seq']).") ";

$sql = "select * from g5_sales3_list where {$sql_search} {$sql_order} ";

$rst = sql_query($sql);
for ($i=0; $row=sql_fetch_array($rst); $i++) {

$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}'");

$release_state = "X";
if($row['wr_release_use'] == 1) {
	$release_state = 'O';
}

$tel = preg_replace('/[^0-9]*/s', '', $row['wr_deli_tel']);

$weight2 = 0;
$singo = 0;

//주문번호에서 A가 포함되어있다면 합배송으로 처리.
if (strpos($row['wr_order_num'], 'A') !== false) {
	
	$sOrdernum = preg_replace("/[^0-9]/", "", $row['wr_order_num']); //합배송 문자열 제거
	$hapSql = "select * from g5_sales3_list where wr_order_num LIKE '%$sOrdernum%'";
	$hapRst = sql_query($hapSql);
	while($hap=sql_fetch_array($hapRst)) {
		
		$singo += $hap['wr_danga'];
		$weight2 += $hap['wr_weight2'];
		
	}
} else {
	$weight2 = $row['wr_weight2'];
	$singo = $row['wr_danga'];
}

$EXCEL_FILE .= "
    <tr>
		<td>".$row['wr_deli_country']."</td>
		<td></td>
		<td></td>
		<td>".$row['wr_order_num']."</td>
		<td></td>
		<td>G.O.TECH(ziotac)</td>
		<td>590315533</td>
		<td>227ho, Centum Leaders Mark , 17, APEC-ro, Haeundae-gu, Busan, Republic of Korea</td>
		<td>".$row['wr_mb_name']."</td>
		<td>".$tel."</td>
		<td></td>
		<td>".$row['wr_deli_zip']."</td>
		<td>".$row['wr_deli_ju']."</td>
		<td>".$row['wr_deli_city']."</td>
		<td>".$row['wr_deli_addr1']." ".$row['wr_deli_addr2']."</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$weight2."</td>
		<td>".$row['wr_box']."</td>
		<td></td>
		<td></td>
		<td></td>
		<td>USD</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$row['wr_name2']."</td>
		<td>".$row['wr_ea']."</td>
		<td>".$singo."</td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$row['wr_hscode']."</td>
		<td>".$row['wr_delivery_fee']."</td>
		<td>".$row['wr_delivery_fee2']."</td>
		<td>1</td>
		<td></td>
		<td></td>
		
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;