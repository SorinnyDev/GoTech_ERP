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
		<td colspan=\"24\"><strong style=\"font-size:16px\">출고등록(FBA)</strong><br> 출력일시 : ".G5_TIME_YMDHIS."</td>
	</tr>
    <tr style=\"background:#f2f2f2\">
		<th style=\"width:70px\">순번</th>
		<th style=\"width:30px\">출고</th>
		<th style=\"width:100px\">도메인명</th>
		<th style=\"width:200px\">주문번호</th>
		<th style=\"width:100px\">랙 번호</th>
		<th style=\"width:100px\">매출일자</th>
		<th style=\"width:100px\">발주일자</th>
		<th style=\"width:100px\">입고일자</th>
		<th style=\"width:100px\">출고일자</th>
		<th style=\"width:150px\">상품코드</th>

		<th style=\"width:150px\">약칭명</th>
		<th style=\"width:250px\">상품명칭</th>
		<th style=\"width:200px\">대표코드</th>

		<th style=\"width:70px\">수량</th>
		<th style=\"width:70px\">박스수</th>
		<th style=\"width:100px\">단가</th>
		<th style=\"width:100px\">신고가격</th>
		<th style=\"width:70px\">통화</th>
		<th style=\"width:70px\">개당무게</th>
		<th style=\"width:70px\">총무게</th>
		<th style=\"width:100px\">배송사</th>
		<th style=\"width:100px\">배송요금</th>
		<th style=\"width:100px\">주문자ID</th>
		<th style=\"width:100px\">주문자명</th>
    </tr>
";



if($date1 && $date2)
	$sql_search .= " and wr_date4 BETWEEN '{$date1}' AND '{$date2}'";

$sql_search .= " and seq IN (".implode(',', $_POST['seq']).") ";

if(!$sst && !$sod) {
	$sst = "seq";
	$sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "select * from g5_sales3_list where (1) {$sql_search} {$sql_order} ";

$rst = sql_query($sql);
for ($i=0; $row=sql_fetch_array($rst); $i++) {

$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}'");

$release_state = "X";
if($row['wr_release_use'] == 1) {
	$release_state = 'O';
}

$rack = sql_fetch("select wr_rack from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$row['wr_product_id']}' GROUP BY wr_rack HAVING SUM(wr_stock) > 0;");

$rack_name = get_rack_name($rack['wr_rack']);

$EXCEL_FILE .= "
    <tr>
		<td style=\"width:70px\">".($i+1)."</td>
		<td  style=\"width:30px;text-align:center\">".$release_state."</td>
		<td  style=\"width:100px\">".$row['wr_domain']."</td>
		<td style=\"width:200px;\">".$row['wr_order_num']."</td>
		<td  style=\"width:100px;\">".$rack_name."</td>
		<td  style=\"width:100px;text-align:center\">".$row['wr_date']."</td>
		<td  style=\"width:100px;text-align:center\">".$row['wr_date2']."</td>
		<td  style=\"width:100px;text-align:center\">".$row['wr_date3']."</td>
		<td  style=\"width:100px;text-align:center\">".$row['wr_date4']."</td>
		<td  style=\"width:150px;text-align:center;".$imsi_item."\">".$item['wr_1']."</td>

		<td  style=\"width:150px;\">".$item['wr_2']."</td>
		<td  style=\"width:250px;".$imsi_item."\">".$item['wr_subject']."</td>
		<td  style=\"width:200px;\">".$item['wr_5']."</td>
		<td  style=\"width:70px;text-align:right\">".$row['wr_ea']."</td>
		<td  style=\"width:70px;text-align:right\">".$row['wr_box']."</td>
		<td  style=\"width:100px;text-align:right\">".$row['wr_danga']."</td>
		<td  style=\"width:100px;text-align:right\">".$row['wr_singo']."</td>
		<td  style=\"width:70px;text-align:center\">".$row['wr_currency']."</td>
		<td  style=\"width:70px;text-align:right\">".$row['wr_weight1']."</td>
		<td  style=\"width:70px;text-align:right\">".$row['wr_weight2']."</td>
		<td  style=\"width:100px;text-align:center\">".$row['wr_delivery']."</td>
		<td  style=\"width:100px;text-align:right\">".$row['wr_delivery_fee']."</td>
		<td  style=\"width:100px;\">".$row['wr_mb_id']."</td>
		<td  style=\"width:100px;\">".$row['wr_mb_name']."</td>
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;