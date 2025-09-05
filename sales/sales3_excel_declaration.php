<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$filename = "declaration_list_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
    <tr style=\"background:#f2f2f2\">
		<th style=\"width:200px\">주문번호</th>
		<th style=\"width:200px\">상품ID</th>
		<th style=\"width:200px\">상품명(영문)</th>
		<th style=\"width:100px\">주문수량</th>
		<th style=\"width:100px\">결제금액</th>
		<th style=\"width:100px\">결제통화코드</th>
		<th style=\"width:100px\">구매자상호명</th>
		<th style=\"width:100px\">목적국 국가코드</th>
		<th style=\"width:200px\">HS코드</th>
		<th style=\"width:100px\">중량</th>
		<th style=\"width:100px\">가격</th>
		<th style=\"width:100px\">도메인명</th>
		<th style=\"width:100px\">제조자</th>
		<th style=\"width:100px\">제조자사업자번호</th>
		<th style=\"width:100px\">제조자사업장일련번호</th>
		<th style=\"width:100px\">제조자통관고유부호</th>
		<th style=\"width:100px\">제조장소(우편번호)</th>
		<th style=\"width:100px\">산업단지부호</th>
		<th style=\"width:100px\">인도조건</th>
		<th style=\"width:100px\">운임원화</th>
		<th style=\"width:100px\">보험료원화</th>
		<th style=\"width:100px\">상품성분명</th>
		<th style=\"width:100px\">주문수량단위</th>
		
		
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

$rack_name = get_rack_name($rack['wr_rack']);

$price = $row['wr_singo'] / $row['wr_ea'];

$EXCEL_FILE .= "
    <tr>
		<td>".$row['wr_order_num']."</td>
		<td>".$item['wr_2']."</td>
		<td>".$row['wr_name2']."</td>
		<td>".$row['wr_ea']."</td>
		<td>".$row['wr_singo']."</td>
		<td></td>
		<td>".$row['wr_mb_name']."</td>
		<td>".$row['wr_deli_country']."</td>
		<td>".$row['wr_hscode']."</td>
		<td>".$row['wr_weight2']."</td>
		<td>".number_format($price, 2)."</td>
		<td>EBAY</td>
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