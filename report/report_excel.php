<?php
include_once('../common.php');

if ($is_guest) {
	alert('로그인 후 이용바랍니다.');
}

$stock_name = PLATFORM_TYPE[$report_type];
$filename = "report_{$stock_name}" . G5_TIME_YMD . ".xls";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Description: PHP4 Generated Data");

$EXCEL_FILE = "
				<table border='1'>

					<tr style=\"background:#ddd\">
						<th style=\"width:100px;text-align:center;\" align=center rowspan='2'>순번</th>
						<th style=\"width:150px\" rowspan='2'>대표코드</th>
						<th style=\"width:100px\" rowspan='2'>SKU</th>
						<th style=\"width:200px\" rowspan='2'>상품명</th>
						<th style=\"width:200px\" rowspan='2'>랙번호</th>
						<th style=\"width:200px\" colspan='2'>한국창고</th>
						<th style=\"width:200px\" colspan='2'>한국창고 금액</th>
						<th style=\"width:200px\" colspan='2'>미국창고</th>
						<th style=\"width:200px\" colspan='2'>미국창고 금액</th>
						<th style=\"width:200px\" colspan='2'>FBA창고</th>
						<th style=\"width:200px\" colspan='2'>FBA창고 금액</th>
						<th style=\"width:200px\" colspan='2'>W-FBA창고</th>
						<th style=\"width:200px\" colspan='2'>W-FBA창고 금액</th>
						<th style=\"width:200px\" colspan='2'>U-FBA창고</th>
						<th style=\"width:200px\" colspan='2'>U-FBA창고 금액</th>
						<th style=\"width:200px\" colspan='2'>임시창고</th>
						<th style=\"width:200px\" colspan='2'>임시창고 금액</th>
                        <th style=\"width:200px\" colspan='2'>한국반품창고</th>
                        <th style=\"width:200px\" colspan='2'>한국반품창고 금액</th>
						<th style=\"width:200px\" colspan='2'>미국반품창고</th>
						<th style=\"width:200px\" colspan='2'>미국반품창고 금액</th>
						<th style=\"width:200px\" colspan='2'>한국폐기창고</th>
						<th style=\"width:200px\" colspan='2'>한국폐기창고 금액</th>
						<th style=\"width:200px\" colspan='2'>미국폐기창고</th>
						<th style=\"width:200px\" colspan='2'>미국폐기창고 금액</th>
						<th style=\"width:200px\" colspan='2'>총 재고량</th>
						<th style=\"width:200px\" colspan='2'>총 재고금액</th>
					</tr>
					<tr style=\"background:#ddd\">
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th colspan='2'>재고</th>
						<th colspan='2'>재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
                        <th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
						<th>잔여재고</th>
						<th>실재고</th>
					</tr>
			";


$sql_common = " from g5_write_product ";
$sql_search = " where (1) ";
$sql_add = "";

if ($stx2) {
	$sql_search .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";
	$sql_add .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";
}

if ($stx2) {
	$stx2 = trim($stx2);
	$sql_search .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%$stx2%' or wr_27 LIKE '%$stx2%' or wr_28 LIKE '%$stx2%' or wr_29 LIKE '%$stx2%' or wr_30 LIKE '%$stx2%' or wr_31 LIKE '%$stx2%' or wr_5 LIKE '%$stx2%' or wr_6 LIKE '%$stx2%' or wr_4 LIKE '%$stx2%' )  ";
	$sql_add .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%$stx2%' or wr_27 LIKE '%$stx2%' or wr_28 LIKE '%$stx2%' or wr_29 LIKE '%$stx2%' or wr_30 LIKE '%$stx2%' or wr_31 LIKE '%$stx2%' or wr_5 LIKE '%$stx2%' or wr_6 LIKE '%$stx2%' or wr_4 LIKE '%$stx2%' )  ";
}

if ($report_type) {
	$report_type = trim($report_type);
	$sql_search .= " AND $report_type != 0 ";
}

if ($report_category) {
	$report_category = trim($report_category);
	$sql_search .= "  AND wr_26 = '$report_category' ";
	$sql_add .= " AND wr_26 = '$report_category'  ";
}

if ($report_mb_id) {
	$report_mb_id = trim($report_mb_id);
	$sql_search .= "  AND mb_id = '$report_mb_id' ";
	$sql_add .= " AND mb_id = '$report_mb_id'  ";
}

if ($search_brand) {
	$sql_search .= "  AND wr_23 = '$search_brand' ";
	$sql_add .= " AND wr_23 = '$search_brand'  ";
}

if ($search_wr_26) {
	$sql_search .= "  AND wr_26 = '$search_wr_26' ";
	$sql_add .= " AND wr_26 = '$search_wr_26'  ";
}

if ($search_warehouse) {

    if (!$search_min && !$search_min) {
        $sql_search .= " AND $search_warehouse > 0";
        $sql_add .= " AND $search_warehouse > 0";
    }

    if ($search_min) {
        $sql_search .= " AND $search_warehouse >= $search_min";
        $sql_add .= " AND $search_warehouse >= $search_min";
    }

    if ($search_max) {
        $sql_search .= " AND $search_warehouse <= $search_max";
        $sql_add .= " AND $search_warehouse <= $search_max";
    }
} else {
    if ($search_min) {
        $sql_search .= " AND (wr_32 >= $search_min OR wr_36 >= $search_min OR wr_42 >= $search_min OR wr_43 >= $search_min OR wr_44 >= $search_min OR wr_37 >= $search_min OR wr_40 >= $search_min OR wr_41 >= $search_min OR wr_45 >= $search_min OR wr_46 >= $search_min 
                OR wr_32_real >= $search_min OR wr_36_real >= $search_min OR wr_42_real >= $search_min OR wr_43_real >= $search_min OR wr_44_real >= $search_min OR wr_40_real >= $search_min  OR wr_41_real >= $search_min OR wr_45_real >= $search_min  OR wr_46_real >= $search_min)";
        $sql_add .= " AND (wr_32 >= $search_min OR wr_36 >= $search_min OR wr_42 >= $search_min OR wr_43 >= $search_min OR wr_44 >= $search_min OR wr_37 >= $search_min OR wr_40 >= $search_min OR wr_41 >= $search_min OR wr_45 >= $search_min OR wr_46 >= $search_min
        OR wr_32_real >= $search_min OR wr_36_real >= $search_min OR wr_42_real >= $search_min OR wr_43_real >= $search_min OR wr_44_real >= $search_min OR wr_40_real >= $search_min  OR wr_41_real >= $search_min OR wr_45_real >= $search_min  OR wr_46_real >= $search_min)";
    }

    if ($search_max) {
        $sql_search .= " AND (wr_32 <= $search_max OR wr_36 <= $search_max OR wr_42 <= $search_max OR wr_43 <= $search_max OR wr_44 <= $search_max OR wr_37 <= $search_max OR wr_40 <= $search_max OR wr_41 <= $search_max OR wr_45 <= $search_max OR wr_46 <= $search_max    
                OR wr_32_real <= $search_max OR wr_36_real <= $search_max OR wr_42_real <= $search_max OR wr_43_real <= $search_max OR wr_44_real <= $search_max OR wr_40_real <= $search_max OR wr_41_real <= $search_max OR wr_45_real <= $search_max OR wr_46_real <= $search_max)";
        $sql_add .= " AND (wr_32 <= $search_max OR wr_36 <= $search_max OR wr_42 <= $search_max OR wr_43 <= $search_max OR wr_44 <= $search_max OR wr_37 <= $search_max  OR wr_40 <= $search_max OR wr_41 <= $search_max OR wr_45 <= $search_max OR wr_46 <= $search_max
                OR wr_32_real <= $search_max OR wr_36_real <= $search_max OR wr_42_real <= $search_max OR wr_43_real <= $search_max OR wr_44_real <= $search_max OR wr_40_real <= $search_max OR wr_41_real <= $search_max OR wr_45_real <= $search_max OR wr_46_real <= $search_max)";
    }
}

if ($return_type === "ko_return") {
    $sql_search .= " and rs.wr_warehouse in ('7000')";
} else if ($return_type === "us_return") {
    $sql_search .= " and rs.wr_warehouse in ('8000')";
}


if (!$sst) {
    $sst = "wr_id";
    $sod = "desc";
}

if ($sst == "stock") {
    $sst = "(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44+wr_40+wr_41+wr_45+wr_46)";
}

if ($sst === "stock_amount") {
    $sst = "(wr_32_amount + wr_36_amount + wr_42_amount + wr_43_amount + wr_44_amount + wr_37_amount + wr_40_amount + wr_41_amount + wr_45_amount + wr_46_amount)";
}

$sql_order = " order by $sst $sod ";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_add} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];



$sql = " select * {$sql_common} {$sql_search} {$sql_order} ";
$rst = sql_query($sql);

for ($i = 0; $row = sql_fetch_array($rst); $i++) {
    $hab_cnt = $row['wr_32'] + $row['wr_36'] + $row['wr_42'] + $row['wr_43'] + $row['wr_44'] + $row['wr_37'] + $row['wr_40'] + $row['wr_41'] + $row['wr_45'] + $row['wr_46'];
    $hab_cnt_real = $row['wr_32_real'] + $row['wr_36_real'] + $row['wr_42_real'] + $row['wr_43_real'] + $row['wr_44_real'] + $row['wr_37'] + $row['wr_40_real'] + $row['wr_41_real'] + $row['wr_45_real']  + $row['wr_46_real'];

	$hab_amount = $hab_cnt * $row['wr_22'];
	$hab__real_amount = $hab_cnt_real * $row['wr_22'];

	$EXCEL_FILE .= "
					<tr>
					<td style=mso-number-format:'\@'>" . ($i + 1) . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_5'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_1'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_subject'] . "</td>
					<td style=mso-number-format:'\@'>" . rack_search($row['wr_id']) . "</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_32']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_32_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_32'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_32_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_36']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_36_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_36'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_36_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_42']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_42_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_42'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_42_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_43']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_43_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_43'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_43_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_44']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_44_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_44'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_44_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@' colspan='2'>" . number_format($row['wr_37']) . "개</td>
					<td style=mso-number-format:'\@' colspan='2'>" . number_format($row['wr_37'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_40']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_40_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_40'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_40_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_41']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_41_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_41'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_41_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_45']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_45_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_45'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_45_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_46']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_46_real']) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_46'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_46_real'] * $row['wr_22']) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($hab_cnt) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($hab_cnt_real) . "개</td>
					<td style=mso-number-format:'\@'>" . number_format($hab_amount) . "원</td>
					<td style=mso-number-format:'\@'>" . number_format($hab__real_amount) . "원</td>
					</tr>
					";
}
if (sql_num_rows($rst) == 0) {
	echo '<tr><td colspan="19">내역이 없습니다.</td></tr>';
}

$EXCEL_FILE .= "</table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;