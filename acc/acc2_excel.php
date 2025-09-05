<?php
include_once('../common.php');

if ($is_guest) {
  alert('로그인 후 이용바랍니다.');
}

$filename = "매입처원장_" . G5_TIME_YMD . ".xls";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Description: PHP4 Generated Data");

$EXCEL_FILE = "
				<table border='1'>

					<tr style=\"background:#ddd\">
						<th style=\"width:100px;text-align:center;\" align=center>순번</th>
						<th style=\"width:150px\">일자</th>
						<th style=\"width:100px\">매입처</th>
						<th style=\"width:200px\">주문번호</th>
						<th style=\"width:200px\">대표코드</th>
						<th style=\"width:200px\">담당자</th>
						<th style=\"width:200px\">SKU</th>
						<th style=\"width:200px\">상품명</th>
						<th style=\"width:200px\">매입액</th>
						<th style=\"width:200px\">지급금</th>
						<th style=\"width:200px\">미지급금</th>
						<th style=\"width:200px\">지급수단</th>
					</tr>
			";


if (!$st_date) {
  $st_date = date("Y-m-d");
}

if (!$ed_date) {
  $ed_date = date("Y-m-d");
}

$sql_where = "";

$qstr = $_SERVER['QUERY_STRING'];
$list = [];
$rows_cnt = 0;
$code = '';
$column = 'wr_orderer';
$sum_misu = 0;
$carryover = 0;

if ($sc_orderer) {
  $sc_arr = explode("|", $sc_orderer);
  $code = $sc_arr[1];
}

if ($code_card) {
  $sql_where .= " AND code.idx = '$code_card'";
}

if ($code) {
  $code = trim($code);
  $sql_where .= " AND sl.wr_orderer = '{$code}'";

  # 이월 합산 금액
  $sql_sum = "
    select sum(wr_misu) as sum_misu
        from g5_sales2_list as sl
             left join g5_write_product as wp on wp.wr_id = sl.wr_product_id
             left join g5_member as m on m.mb_id = sl.mb_id
        where wr_direct_use = '0'
          and wr_date3 >= '2025-01-01' and wr_date3 < '$st_date' and  wr_orderer = '$code'  
  ";

  $result = sql_fetch($sql_sum);

  $sum_misu = $result['sum_misu'];

  # 이월 합산 금액 보정
  $sql = "SELECT sum(amount) as amount FROM g5_acc2_carryover_amount WHERE wr_orderer = '{$code}' and base_date <= '{$st_date}'";
  $result = sql_fetch($sql);

  $carryover = $result['amount'];

}

if ($search_value) {
  $search_value = trim($search_value);
  $sql_where .= " AND (sl.wr_order_num = '$search_value' OR IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wr_subject, sl.wr_product_nm) LIKE '%$search_value%' OR IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) = '$search_value')";
}

if ($search_mb_id) {
  $search_mb_id = trim($search_mb_id);
  $sql_where .= " AND sl.mb_id = '$search_mb_id'";
}

$query = "
    select sl.seq,
           wr_date3,
           sl.wr_orderer,
           (SELECT code_name FROM g5_code_list WHERE idx = sl.wr_orderer) as wr_orderer_nm,
           sl.wr_order_num,
           sl.wr_code,
           IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) as code,
           IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wr_subject, sl.wr_product_nm) as product_nm,
           sl.wr_order_total,
           IFNULL(wr_warehouse_price, 0) as pay_price,
           wr_misu,
           mb_name,
           code.code_name
    from g5_sales2_list as sl
             left join g5_write_product as wp on wp.wr_id = sl.wr_product_id
						 left join g5_member as m on m.mb_id = sl.mb_id
             left join g5_sales1_list as sl1 on sl1.wr_order_num = sl.wr_order_num
             left join g5_sales_metadata as sm on sm.entity_type = 'g5_sales1_list' and entity_id = sl1.seq and `key` = 'code_card'
             left join g5_code_list as code on code.code_type = '7' and code.idx = sm.value
    where wr_direct_use = '0'
      and wr_date3 between '{$st_date}' and '{$ed_date}' {$sql_where}
    order by wr_date3
";

$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
  $list[$row['wr_orderer']][$row['wr_date3']][$row['seq']] = $row;
}

$index = 0;
foreach ($list as $k => $v) {
  foreach ($v as $k2 => $v2) {
    $date = $k2;
    $rowspan = count($v2);
    $row1 = true;
    $rowspan2 = count($v2);
    $row2 = true;

    $tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
    foreach ($v2 as $k3 => $row) {

      $EXCEL_FILE .= "
					<tr>
					<td style=mso-number-format:'\@'>" . (++$index) . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_date3'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_orderer_nm'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_order_num'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['wr_code'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['mb_name'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['code'] . "</td>
					<td style=mso-number-format:'\@'>" . $row['product_nm'] . "</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_order_total']) . "</td>
					<td style=mso-number-format:'\@'>" . number_format($row['pay_price']) . "</td>
					<td style=mso-number-format:'\@'>" . number_format($row['wr_misu']) . "</td>
					<td style=mso-number-format:'\@'>" . $row['code_name'] . "</td>
					</tr>
					";

    }
  }
}

if (count($list) == 0) {
  echo '<tr><td colspan="19">내역이 없습니다.</td></tr>';
}

$EXCEL_FILE .= "</table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;