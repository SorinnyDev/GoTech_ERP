<?php
include_once('../common.php');

if ($is_guest) {
  alert('로그인 후 이용바랍니다.');
}

$filename = "폐기관리_" . G5_TIME_YMD . ".xls";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Description: PHP4 Generated Data");

$EXCEL_FILE = "
				<table border='1'>

					<tr style=\"background:#ddd\">
						<th style=\"width:100px\">폐기일자</th>
						<th style=\"width:200px\">대표코드</th>
						<th style=\"width:100px\">수량</th>
						<th style=\"width:300px\">LABEL</th>
						<th style=\"width:200px\">단가</th>
						<th style=\"width:200px\">매입가</th>
					</tr>
			";


if (!$mode) {
  $mode = 0;
}

if (!$date1) {
  $date1 = date("Y-m-01");
}

if (!$date2) {
  $date2 = date("Y-m-d");
}

$where = ' (1) ';

if ($date1) {
  $where .= " and date_format(dl.wr_datetime, '%Y-%m-%d') >= '$date1' ";
}

if ($date2) {
  $where .= " and date_format(dl.wr_datetime, '%Y-%m-%d') <= '$date2' ";
}

$warehouse = '';

if ($mode == 1) {
  $warehouse = "한국 폐기창고";
} else if ($mode == 2) {
  $warehouse = "미국 폐기창고";
}

if ($warehouse) {
  $where .= " and ds.wr_warehouse like '%$warehouse%'";
}

$query = "
  select dl.id, dl.wr_datetime, dl.wr_stock, wr_subject, wr_1, wp.wr_5, ds.wr_warehouse, ds.wr_rack, dl.wr_memo, dl.product_id, di.id as 'img_id', di.wr_img1, wp.wr_22 + 0 as 'wr_22' from g5_discard_list as dl
  left join g5_write_product as wp on wp.wr_id = dl.product_id
  left join g5_discard_img as di on di.discard_id = dl.id  
  left join g5_discard_stock as ds on ds.discard_id = dl.id
  where {$where}                                                                                                                
  group by dl.id
  order by dl.wr_datetime desc
";

$list = sql_fetch_all($query);

$index = 0;
foreach ($list as $k => $item) {

  $EXCEL_FILE .= "
					<tr>
					<td style=mso-number-format:'\@'>" . $item['wr_datetime'] . "</td>
					<td style=mso-number-format:'\@'>" . ($item['wr_5'] ?? $item['wr_subject']) . "</td>
					<td style=mso-number-format:'\@'>" . $item['wr_stock'] . "</td>
					<td style=mso-number-format:'\@'>" . $item['wr_subject'] . "</td>
					<td style=mso-number-format:'\@'>" . number_format($item['wr_22']) . "</td>
					<td style=mso-number-format:'\@'>" . number_format($item['wr_22'] * $item['wr_stock']) . "</td>
					</tr>
					";

}

if (count($list) == 0) {
  echo '<tr><td colspan="6">내역이 없습니다.</td></tr>';
}

$EXCEL_FILE .= "</table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;