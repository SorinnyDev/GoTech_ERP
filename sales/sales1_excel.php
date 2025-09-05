<?php
include_once('./_common.php');

if ($is_guest) {
    alert('로그인 후 이용바랍니다.');
}

$filename = "sales1_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

# 파라메터 받기
$mb_id = $_REQUEST['mb_id']; // 담당자
$domain = $_REQUEST['wr_18']; // 도메인
$orderchk = $_REQUEST['orderchk']; // 발주
$dType = $_REQUEST['dType']; // 유통
$wr_order_num = $_REQUEST['stx']; // 주문번호
$date1 = $_REQUEST['date1']; // 발주일자 조회 시작일
$date2 = $_REQUEST['date2']; // 발주일자 조회 종료일

$sql_where = "";
if (isDefined($mb_id)) {
    $sql_where .= " AND A.mb_id='".$mb_id."' ";
}

if (isDefined($domain)) {
    $sql_where .= " AND A.wr_domain='".$domain."' ";
}

if (isDefined($orderchk)) {
    if ($orderchk == 1) {
        $sql_where .= " and A.wr_order_ea > 0 ";
    } elseif ($orderchk == 2) {
        $sql_where .= " and A.wr_order_ea = 0  ";
    } elseif ($orderchk == 3) {
        $sql_where .= " and A.wr_order_etc != ''  ";
    }
}

if (isDefined($dType)) {
    if ($dType == "1") {
        $sql_where .= " AND A.wr_domain NOT IN('".implode("','", $circulation)."') ";
    } elseif ($dType == "2") {
        $sql_where .= " AND A.wr_domain IN('".implode("','", $circulation)."') ";
    }
}

if (isDefined($wr_order_num)) {
    $sql_where .= " and A.wr_order_num LIKE '%".$wr_order_num."%' ";
}

if ($seq && count($seq) > 0) {
    $sql_where .= " and A.seq in ('". implode("','", $seq) ."') ";
}

if (!$sst && !$sod) {
    $sst = "A.seq";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "SELECT A.*,B.mb_name,C.wr_2,C.wr_subject,C.wr_32,C.wr_36 FROM g5_sales1_list A \n";
$sql .= "LEFT OUTER JOIN g5_member B on B.mb_id=A.mb_id \n";
$sql .= "LEFT OUTER JOIN g5_write_product C ON C.wr_id=A.wr_product_id \n";
$sql .= "WHERE (1) AND wr_date2 BETWEEN '".$date1."' AND '".$date2."' ".$sql_where;
$sql .= "group by A.seq {$sql_order}";
// $sql .= "ORDER BY A.wr_date2 ASC,A.wr_order_num ASC";
$rs = sql_query($sql);
?>
<table border="1">
	<thead>
		<tr>
			<th class="sticky-th" style="width:50px;">순번</th>
			<th class="sticky-th" style="width:50px;">발주</th>
			<th class="sticky-th" style="width:150px;">도메인명</th>
			<th class="sticky-th" style="width:150px;">주문번호</th>
			<th class="sticky-th" style="width:100px;">매출일자</th>
			<th class="sticky-th" style="width:100px;">발주일자</th>
			<th class="sticky-th" style="width:80px;">담당자</th>
			<th class="sticky-th" style="width:100px;">한국재고</th>
			<th class="sticky-th" style="width:100px;">안전재고</th>
			<th class="sticky-th" style="width:100px;">미국재고</th>
			<th class="sticky-th" style="width:100px;">매출수량</th>
			<th class="sticky-th" style="width:500px;">상품코드</th>
			<th class="sticky-th" style="width:300px;">약칭명</th>
			<th class="sticky-th" style="width:300px;">상품명칭</th>
			<th class="sticky-th" style="width:500px;">대표코드</th>
			<th class="sticky-th" style="width:100px;">발주수량</th>
			<th class="sticky-th" style="width:100px;">박스수</th>
			<th class="sticky-th" style="width:100px;">단가</th>
			<th class="sticky-th" style="width:100px;">신고가격</th>
			<th class="sticky-th" style="width:100px;">통화</th>
			<th class="sticky-th" style="width:100px;">개당무게</th>
			<th class="sticky-th" style="width:100px;">총무게</th>
			<th class="sticky-th" style="width:100px;">배송사</th>
			<th class="sticky-th" style="width:100px;">배송요금</th>
			<th class="sticky-th" style="width:200px;">주문자ID</th>
			<th class="sticky-th" style="width:200px;">주문자명</th>
		</tr>
	</thead>
	<tbody>
		<?if (sql_num_rows($rs) > 0) {?>
			<?php
            $num = 1;
		    while ($row = sql_fetch_array($rs)) {?>
				<?php
		        # 안전 재고 없을 경우
		        $date = new DateTime($row['wr_date2']);
		        $date->modify('-1 days');
		        $ed_date = $date->format('Y-m-d');

		        $date = new DateTime($ed_date);
		        $date->modify('-3 months');
		        $st_date = $date->format('Y-m-d');

		        $product_id = $row['wr_product_id'];
		        $sql = "
						SELECT
						SUM(G0.wr_ea) AS total_sales_ea
						FROM g5_sales3_list G0
						LEFT JOIN g5_write_product WP ON WP.wr_id = G0.wr_product_id
						WHERE wr_release_use = '1'
						AND G0.wr_date4 BETWEEN '$st_date' AND '$ed_date' 
						AND G0.wr_product_id = '{$product_id}'
						GROUP BY
						G0.wr_product_id    
						";

		        $safe_result = sql_fetch($sql);
		        $safe_ea = round(($safe_result['total_sales_ea'] / 3) * 2);
		        $row['wr_safe_ea'] = $safe_ea;
		        ?>
				<tr>
					<td align="center"><?=$num?></td>
					<td align="center"><?=($row['wr_order_ea'] > 0) ? "O" : "X"?></td>
					<td align="center"><?=$row['wr_domain']?></td>
					<td align="center"><?=$row['wr_order_num']?></td>
					<td align="center"><?=$row['wr_date']?></td>
					<td align="center"><?=$row['wr_date2']?></td>
					<td align="center"><?=$row['mb_name']?></td>
					<td align="center"><?=$row['wr_32']?></td>
					<td align="center"><?=$row['wr_safe_ea']?></td>
					<td align="center"><?=$row['wr_36']?></td>
					<td align="center"><?=($row['wr_etc_chk'] == "0") ? $row['wr_ea'] : "0"?></td>
					<td><?=$row['wr_code']?></td>
					<td><?=$row['wr_2']?></td>
					<td><?=$row['wr_subject']?></td>
					<td><?=$row['wr_code']?></td>
					<td align="center"><?=$row['wr_order_ea']?></td>
					<td align="center"><?=$row['wr_box']?></td>
					<td align="center"><?=$row['wr_danga']?></td>
					<td align="center"><?=$row['wr_singo']?></td>
					<td align="center"><?=$row['wr_currency']?></td>
					<td align="center"><?=$row['wr_weight1']?></td>
					<td align="center"><?=$row['wr_weight2']?></td>
					<td><?=$row['wr_delivery']?></td>
					<td><?=$row['wr_delivery_fee']?></td>
					<td align="center"><?=$row['wr_mb_id']?></td>
					<td align="center"><?=$row['wr_mb_name']?></td>
				</tr>
			<?php
		                    $num++;
		    }
		    ?>
		<?} else {?>
			<tr>
				<td colspan="25">조회되는 데이터가 없습니다.</td>
			</tr>
		<?}?>
	</tbody>
</table>
