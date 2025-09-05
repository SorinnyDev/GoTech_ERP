<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$filename = "출고미완료건_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	<tr>
		<td colspan=\"33\"><strong style=\"font-size:16px\">출고등록(한국)</strong><br> 출력일시 : ".G5_TIME_YMDHIS."</td>
	</tr>
    <tr style=\"background:#f2f2f2\">
		<th style=\"width:100px\">도메인</th>
		<th style=\"width:100px\">창고</th>
		<th style=\"width:100px\">랙</th>
		<th style=\"width:100px\">주문번호</th>
		<th style=\"width:100px\">바로출고유무</th>
		<th style=\"width:100px\">입고등록일</th>
		<th style=\"width:100px\">출고등록일</th>
		<th style=\"width:100px\">출고등록유무</th>
    </tr>
";

$sql_where = "";
if(isDefined($wr_id)){
	$sql_where .= " AND A.wr_product_id='".$wr_id."' ";
}

$sql = "SELECT A.wr_domain,A.wr_warehouse,A.wr_order_num,A.wr_date3,IFNULL(B.wr_date4,'') AS wr_date4, IF(B.wr_release_use IS NULL,'출고등록 전','출고등록') AS chul_yn,A.wr_direct_use,C.gc_name \n";
$sql .= "FROM g5_sales2_list A \n";
$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num \n";
$sql .= "LEFT OUTER JOIN g5_rack C ON C.seq=A.wr_rack \n";
$sql .= "WHERE A.wr_etc_use = '0' AND IFNULL(B.wr_release_use,'0') = '0' ".$sql_where." ORDER BY A.wr_date3 ASC";
$rs = sql_query($sql);
while($row = sql_fetch_array($rs)){
	if($row['wr_warehouse'] == "1000"){
		$ware = "한국창고";
	}else if($row['wr_warehouse'] == "3000"){
		$ware = "미국창고";
	}else if($row['wr_warehouse'] == "4000"){
		$ware = "FBA창고";
	}else if($row['wr_warehouse'] == "5000"){
		$ware = "W-FBA창고";
	}else if($row['wr_warehouse'] == "6000"){
		$ware = "U-FBA창고";
	}

	$EXCEL_FILE .= "
		<tr>
			<td style=\"width:100px\">".$row['wr_domain']."</td>
			<td style=\"width:100px\">".$ware."</td>
			<td style=\"width:100px\">".(($row['wr_direct_use'] == "1")?$row['gc_name']:"")."</td>
			<td style=\"width:100px\">".$row['wr_order_num']."</td>
			<td style=\"width:100px\">".(($row['wr_direct_use'] == "0")?"발주 후 출고":"바로 출고")."</td>
			<td style=\"width:100px\">".$row['wr_date3']."</td>
			<td style=\"width:100px\">".$row['wr_date4']."</td>
			<td style=\"width:100px\">".$row['chul_yn']."</td>
		</tr>
	";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;
?>