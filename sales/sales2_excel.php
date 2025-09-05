<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');


$filename = "order_list_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	<tr>
		<td colspan=\"22\"><strong style=\"font-size:16px\">발주자료</strong><br> 조회기간 : ".$wr_19_s."~".$wr_19_e."</td>
	</tr>
	<tr style=\"background:#f2f2f2\">
		<th style=\"width:100px\">매출일자</th>
		<th style=\"width:100px\">발주일자</th>
		<th style=\"width:100px\">도메인명</th>
		<th style=\"width:150px\">SKU</th>
		<th style=\"width:400px\">상품명</th>
		<th style=\"width:70px\">발주수량</th>
		<th style=\"width:70px\">주문수량</th>
		<th style=\"width:200px\">주문번호</th>
		<th style=\"width:100px\">특송</th>
		<th style=\"width:150px\">주문자명</th>
		<th style=\"width:70px\">단가</th>
		<th style=\"width:70px\">신고가격</th>
		<th style=\"width:70px\">개당무게</th>
		<th style=\"width:70px\">총 무게</th>
		<th style=\"width:150px\">HS CODE</th>
		<th style=\"width:150px\">나라명</th>
	
	</tr>
    
";


if($wr_18){
	$sql_search .= " and a.wr_domain = '{$wr_18}'";
}
if($mb_id){
	$sql_search .= " and b.mb_id = '{$mb_id}'";
}
if($wr_19_s && $wr_19_e) {
	$sql_search .= " and a.wr_date2 BETWEEN '{$wr_19_s}' AND '{$wr_19_e}' ";
}

if($stx) {
	$sql_search .= " AND ( a.wr_order_num LIKE '%$stx%' or b.wr_1  LIKE '%$stx%' or b.wr_5  LIKE '%$stx%' ) ";
}

if($tracking_no){
	$sql_search .= " AND wr_order_traking LIKE '%".$tracking_no."%'";
}

$sql_search .= " and a.wr_warehouse != '3000'";
$sql = "select * from g5_sales1_list a
LEFT JOIN g5_write_product b ON b.wr_id=a.wr_product_id 

where (1) {$sql_search} and a.wr_date != '' and wr_chk = 0 order by a.seq desc";

$rst = sql_query($sql); 
for($i=0; $row=sql_fetch_array($rst); $i++) {

	$chk = sql_fetch("select * from g5_sales2_list where wr_order_num = '{$row['wr_order_num']}' and wr_set_sku = ''");
								
	if($chk){
		continue;
	}
	
	$item = sql_fetch("select * from g5_write_product where (wr_1 = '{$row['wr_code']}' or wr_27 = '{$row['wr_code']}' or wr_28 = '{$row['wr_code']}' or wr_29 = '{$row['wr_code']}' or wr_30 = '{$row['wr_code']}' or wr_31 = '{$row['wr_code']}')");

  if ($row['wr_servicetype'] === '0001') {
    $express_title = '&#10004;';
  } else {
    $express_title = '';
  }


  $bg = "";
	$ea_chk = "";
	$disabled = "";
	

$EXCEL_FILE .= "
    <tr>
		<td>".substr($row['wr_date'],0,10)."</td>
		<td>".substr($row['wr_date2'],0,10)."</td>
		<td>".$row['wr_domain']."</td>
		<td>".$row['wr_code']."</td>
		<td>".$item['wr_subject']."</td>
		<td>".$row['wr_order_ea']."</td>
		<td>".$row['wr_ea']."</td>
		<td>".$row['wr_order_num']."</td>
		<td>".$express_title."</td>
		<td>".$row['wr_mb_name']."</td>
		<td>".$row['wr_danga']."</td>
		<td>".$row['wr_singo']."</td>
		<td>".$row['wr_weight1']."</td>
		<td>".$row['wr_weight2']."</td>
		<td>".$row['wr_hscode']."</td>
		<td>".$row['wr_country']."</td>
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;