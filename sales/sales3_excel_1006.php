<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$filename = "fedex_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	
    <tr style=\"background:#ddd\">
		<th style=\"width:200px\">주문자명</th>
		<th style=\"width:100px\">관세/세금청구 대상</th>
		<th style=\"width:100px\">발송조건</th>
		<th style=\"width:150px\">참조넘버 및 메모</th>
		<th style=\"width:70px\">트래킹 넘버</th>
		<th style=\"width:70px\">예약넘버</th>
		<th style=\"width:100px\">발송자명</th>
		<th style=\"width:70px\"></th>
		<th style=\"width:100px\">수취인명</th>
		<th style=\"width:100px\">수취인 회사명</th>
		<th style=\"width:100px\">주소(1)</th>
		<th style=\"width:70px\">주소(2)</th>
		<th style=\"width:70px\">주소(3)</th>
		<th style=\"width:100px\">도시명</th>
		<th style=\"width:100px\">국가코드</th>
		<th style=\"width:100px\">우편번호</th>
		<th style=\"width:100px\">전화번호</th>
		<th style=\"width:70px\"></th>
		<th style=\"width:100px\">이메일</th>
		<th style=\"width:100px\">발송타입</th>
		<th style=\"width:200px\">품목명</th>
		<th style=\"width:100px\">HS CODE</th>
		<th style=\"width:100px\">배송비</th>
		<th style=\"width:100px\">추가 배송비</th>
		<th style=\"width:100px\">수량</th>
		<th style=\"width:100px\">제조국가</th>
		<th style=\"width:100px\">단가</th>
		<th style=\"width:100px\">화폐단위</th>
		<th style=\"width:100px\">개당무게</th>
		<th style=\"width:100px\">인이스 넘버</th>
		<th style=\"width:100px\">CI항목</th>
		<th style=\"width:100px\">CI항목</th>
		<th style=\"width:70px\">개수 단위</th>
		<th style=\"width:70px\">박스갯수</th>
		<th style=\"width:70px\">총 무게</th>
		<th style=\"width:70px\">가로</th>
		<th style=\"width:70px\">세로</th>
		<th style=\"width:70px\">높이</th>
		<th style=\"width:70px\">2P/EC</th>
		<th style=\"width:70px\">포장방식</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">-</th>
		<th style=\"width:70px\">배송서비스</th>
		<th style=\"width:70px\">-</th>
	
		
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

$ec = "2P";

if(in_array($row['wr_country'], array("JAPAN", "JP", "TH", "AU", "NZ", "HK", "CN", "TW", "SG" ))) {
	$ec = "EC";
}


$EXCEL_FILE .= "
    <tr>
		<td>".$row['wr_order_num']." ".$item['wr_2']."</td>
		<td></td>
		<td></td>
		<td>".$row['wr_order_num']." ".$item['wr_2']."</td>
		<td></td>
		<td></td>
		<td>ziotac</td>
		<td></td>
		<td>".$row['wr_mb_name']."</td>
		<td>".$row['wr_mb_name']."</td>
		<td>".$row['wr_deli_addr1']." ".$row['wr_deli_addr2']."</td>
		<td></td>
		<td></td>
		<td>".$row['wr_deli_city']."</td>
		<td>".$row['wr_deli_country']."</td>
		<td>".$row['wr_deli_zip']."</td>
		<td>".$tel."</td>
		<td></td>
		<td>".$row['wr_email']."</td>
		<td></td>
		<td>".$item['wr_2']."</td>
		<td>".$row['wr_hscode']."</td>
		<td>".$row['wr_delivery_fee']."</td>
		<td>".$row['wr_delivery_fee2']."</td>
		<td>".$row['wr_ea']."</td>
		<td>KR</td>
		<td>".$singo."</td>
		<td>".$row['wr_currency']."</td>
		<td>".$row['wr_weight1']."</td>
		<td></td>
		<td></td>
		<td></td>
		<td>EA</td>
		<td>1</td>
		<td>".$weight2."</td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$ec."</td>
		<td>1</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;