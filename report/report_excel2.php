<?php 
include_once('../common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$stock_name = PLATFORM_TYPE[$report_type];    
$filename = "report_{$stock_name}".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");

$rowspan1 = " rowspan=\"1\" ";
$rowspan2 = " rowspan=\"2\" ";

$EXCEL_FILE = "
<table border='1'>
	
    <tr style=\"background:#ddd\">
		<th {$rowspan2} style=\"width:100px;text-align:center;\" align=center>순번</th>
		<th {$rowspan2} style=\"width:150px\">대표코드</th>
		<th {$rowspan2} style=\"width:100px\">SKU</th>
		<th {$rowspan2} style=\"width:200px\">상품명</th>
		<th {$rowspan2} style=\"width:200px\">랙번호</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
		<th {$rowspan1} style=\"width:100px\">실사재고조사</th>
    </tr>
    <tr>
        <td  {$rowspan1}>한국창고</td>
        <td  {$rowspan1}>미국창고</td>
        <td  {$rowspan1}>FBA창고</td>
        <td  {$rowspan1}>W-FBA창고</td>
        <td  {$rowspan1}>U-FBA창고</td>
        <td  {$rowspan1}>임시창고</td>
        <td  {$rowspan1}>총 재고량</td>
    </tr>
";


$sql_common = " from g5_write_product ";
$sql_search = " where (1) ";
$sql_add = "";

if($stx2) {
    $sql_search .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";
    $sql_add .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";
}

if($report_type){
    $sql_search .= " AND {$report_type} != 0 ";
}

if($report_category){
    $sql_search .= "  AND wr_26 = '{$report_category}' ";
    $sql_add .= " AND wr_26 = '{$report_category}'  ";
}

if($report_mb_id){
    $sql_search .= "  AND mb_id = '{$report_mb_id}' ";
    $sql_add .= " AND mb_id = '{$report_mb_id}'  ";
}

if (!$sst) {
    $sst  = "wr_id";
    $sod = "desc";
}

if($sst == "stock")
    $sst = "(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44)";

$sql_order = " order by $sst $sod ";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_add} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
$rst = sql_query($sql);

for ($i=0; $row=sql_fetch_array($rst); $i++) {
    $hab_cnt = $row['wr_32'] + $row['wr_36'] + $row['wr_42'] + $row['wr_43'] + $row['wr_44'] + $row['wr_37'];
    
    $EXCEL_FILE .= "
        <tr>
            <td style=mso-number-format:'\@' {$rowspan2}>".($i+1)."</td>
            <td style=mso-number-format:'\@' {$rowspan2}>".$row['wr_5']."</td>
            <td style=mso-number-format:'\@' {$rowspan2}>".$row['wr_1']."</td>
            <td style=mso-number-format:'\@' {$rowspan2}>".$row['wr_subject']."</td>
            <td style=mso-number-format:'\@' {$rowspan2}>".rack_search($row['wr_id'])."</td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
            <td style=mso-number-format:'\@' {$rowspan1}></td>
        </tr>
        <tr>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_32'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_36'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_42'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_43'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_44'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($row['wr_37'])."개</td>
            <td style=mso-number-format:'\@' {$rowspan1}>".number_format($hab_cnt)."개</td>
        </tr>
    ";
}
if (sql_num_rows($rst) == 0) { echo '<tr><td colspan="12">내역이 없습니다.</td></tr>'; }

$EXCEL_FILE .= "</table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;