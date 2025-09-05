<?php
include_once("./_common.php");

if (!$st_date || !$ed_date) {
  echo "
	<tr>
		<td colspan='9'>도메인과 검색일자를 선택해주세요.</td>
	</tr>
  ";
  exit;
}

// 파라미터 받기
$wr_domain = $wr_18;
$sql_where = "";

if (!empty($wr_domain)) {
  $sql_where .= " AND A.wr_domain = '{$wr_domain}' ";
}

if (!empty($ordernum)) {
  $sql_where .= " AND A.wr_order_num like '%{$ordernum}%' ";
}

// 환율 정보 수집
$exData = [];
$today = date("Y-m-d");
$date_arr = date_to_date($st_date, $ed_date);

foreach ($date_arr as $v) {
  $ex_date = $v['date'];
  $rowData = [];

  if ($today == $ex_date) {
    $exRs = sql_query("SELECT * FROM g5_excharge");
  } else {
    $table = "g5_excharge_log";
    $exRs = sql_query("SELECT * FROM {$table} WHERE ex_date='{$ex_date}'");
    if (sql_num_rows($exRs) == 0) {
      $exRs = sql_query("SELECT * FROM g5_excharge");
    }
  }

  while ($exRow = sql_fetch_array($exRs)) {
    $currency = $exRow['ex_eng'];
    if ($currency === "JPY") {
      $exRow['rate'] = str_replace(",", "", $exRow['rate']) * 0.01;
    }
    $rowData[$currency] = $exRow;
  }

  $exData[$ex_date] = $rowData;
}

// 매출 데이터 조회
$sql = "
SELECT  
    A.wr_date4, A.wr_danga, A.wr_paymethod, A.wr_domain, A.wr_currency,
    C.wr_1 AS sku, C.wr_5 AS p_code,
    IF(A.wr_set_sku != '', A.wr_set_sku, A.wr_order_num) AS 'set',
    A.wr_set_sku,
    A.wr_ori_order_num, A.wr_singo, 
    REPLACE(IF(A.wr_exchange_rate = '' OR A.wr_exchange_rate = 0, E.rate, A.wr_exchange_rate), ',', '') AS wr_exchange_rate,
    A.wr_order_num, 
    IFNULL(B.ibgo_danga, C.wr_22) AS danga,
    A.wr_tax, A.wr_shipping_price, A.wr_currency,
    A.wr_delivery_fee, A.wr_delivery_fee2, 
    A.wr_delivery_oil, C.wr_subject,
    IFNULL(B.chul_ea, A.wr_ea) AS chul_ea,
    A.seq as sales3_seq
FROM g5_sales3_list A
LEFT JOIN g5_sales3_det B ON B.sales3_id=A.seq AND B.del_yn='N'
LEFT JOIN g5_write_product C ON C.wr_id=A.wr_product_id
LEFT JOIN g5_write_product_fee D ON D.wr_id=C.wr_id AND D.warehouse=A.wr_warehouse
LEFT JOIN g5_excharge E ON E.ex_eng = A.wr_currency
WHERE A.wr_release_use='1' {$sql_where} 
AND A.wr_date4 BETWEEN '{$st_date}' AND '{$ed_date}' 
ORDER BY A.wr_domain, A.wr_date4 ASC, A.wr_order_num ASC, A.wr_ori_order_num ASC
";

$rs = sql_query($sql);

// 데이터 정리
$list = [];
$wr_ori_order_num_list = '';
$wr_order_num_list = '';

while ($row = sql_fetch_array($rs)) {
  $wr_ori_order_num = $row['wr_ori_order_num'];
  $wr_order_num = $row['wr_order_num'];
  $set = $row['set'];

  $list[$wr_ori_order_num]['data'][$set]['data'][$wr_order_num]['data'][] = $row;

  $wr_ori_order_num_list .= ',' . $wr_ori_order_num;
  $wr_order_num_list .= ',' . $wr_order_num;
}

// 주문 수량 카운팅
foreach ($list as $k => $v) {
  foreach ($v['data'] as $k2 => $v2) {
    foreach ($v2['data'] as $k3 => $v3) {
      $cnt = count($v3['data']);
      $list[$k]['data'][$k2]['data'][$k3]['cnt'] += $cnt;
      $list[$k]['data'][$k2]['cnt'] += $cnt;
      $list[$k]['cnt'] += $cnt;
    }
  }
}

$total_basic_fee = 0;
$total_sales_fee = 0;
$total_delivery_fee = 0;
$total_delivery_fee2 = 0;
$total_sales_price = 0;
$total_tax = 0;
$total_pm_price = 0;
$total_rate = 0;
$total_ea = 0;
$total_ibgo_price = 0;
//부가세환급
$total_buga_price = 0;
$set_sales_price = 0;
$sales3_det_num_count = 1;

$result = sql_fetch_all($sql);

$chknum = 1;


$current_group = '';
$current_order_group = '';
$group_subtotal = [
  'basic_fee' => 0,
  'delivery_fee' => 0,
  'delivery_fee2' => 0,
  'ibgo_price' => 0,
  'sales_price' => 0,
  'tax' => 0,
  'pm_price' => 0,
  'buga_price' => 0,
  'ea' => 0
];


$sets = [];

# 하나의 주문 건 안에 상품 입고 단가 다를 때 담기 위한 배열
$duplicate_check_list = [];
foreach ($result as &$row) {
  $ori_id = $row['wr_ori_order_num'];

  # 하나의 주문 건 안에 입고단가 다를 경우 중복 제외
  if (!in_array($row['sales3_seq'], $duplicate_check_list)) {
    $duplicate_check_list[] = $row['sales3_seq'];
  } else {
    $row['wr_singo'] = 0;
    $row['is_duplicate_det'] = true;
  }

  $sets[$ori_id]['rows'][] = $row;

  $exchange_rate = (float)$row['wr_exchange_rate'];
  $wr_singo_rate = (float)$row['wr_singo'] * $exchange_rate;

  switch ($row['wr_paymethod']) {
    case "Shopify Payments":
    case "Shop Cash":
    case "Shop Cash + Shopify Payments":
      $basic_fee = $row['wr_singo'] * 0.024 + 0.3;
      break;
    case "PayPal Express Checkout":
    case "Shopify Payments + PayPal Express Checkout":
      $basic_fee = $row['wr_singo'] * 0.0349 + 0.49;
      break;
    default:
      $basic_fee = 0;
  }

  $basic_fee_rate = $basic_fee * $exchange_rate;
  $wr_delivery_fee = $row['wr_delivery_fee'];
  $wr_delivery_fee2 = $row['wr_delivery_fee2'];
  $ibgo_price = $row['danga'] * $row['chul_ea'];
  $buga_price = $ibgo_price * 0.1;

  $is_set = !empty($row['wr_set_sku']);

  if (!isset($sets[$ori_id]['tax_calculated'])) {
    // 세트(합배송, set상품) 단위로 TAX 한 번만 계산 (모든 상품의 wr_tax를 더하지 않음)
    $first_tax_row = $row;
    $sets[$ori_id]['summary']['tax'] = floor($first_tax_row['wr_tax'] * $exchange_rate);
    $sets[$ori_id]['summary']['tax_u'] = $first_tax_row['wr_tax'];
    $sets[$ori_id]['summary']['wr_shipping_price'] = $row['wr_shipping_price'];
    $sets[$ori_id]['tax_calculated'] = true;
  }

  if ($is_set) {
    $sets[$ori_id]['summary']['fee'] = $basic_fee_rate;
    $sets[$ori_id]['summary']['singo'] = $row['wr_singo'];
    $sets[$ori_id]['summary']['danga'] = $row['wr_danga'];
  } else {
    $sets[$ori_id]['summary']['fee'] += $basic_fee_rate;
    $sets[$ori_id]['summary']['singo'] += $row['wr_singo'];
    $sets[$ori_id]['summary']['danga'] += $row['wr_danga'];
  }

  $is_duplicate = false;

  $prev_list = $sets[$ori_id]['rows'];
  array_pop($prev_list);

  foreach ($prev_list as $item) {
    if ($row['wr_order_num'] === $item['wr_order_num']) {
      $is_duplicate = true;
    }
  }

  $sets[$ori_id]['summary']['buga'] += $buga_price;
  $sets[$ori_id]['summary']['delivery1'] += $wr_delivery_fee;
  $sets[$ori_id]['summary']['delivery2'] += $wr_delivery_fee2;
  $sets[$ori_id]['summary']['ibgo'] += $ibgo_price;
  $sets[$ori_id]['summary']['ea'] += $row['chul_ea'];

  if (!$is_duplicate) {
    $sets[$ori_id]['summary']['sales'] += $wr_singo_rate;
  }


}

unset($row);

$total = [
  'sales' => 0, 'buga' => 0, 'tax' => 0,
  'fee' => 0, 'delivery1' => 0, 'delivery2' => 0,
  'ibgo' => 0, 'ea' => 0, 'pm' => 0, 'tax_u' => 0, 'wr_shipping_price' => 0
];

foreach ($sets as $ori_id => $set) {
  $summary = $set['summary'];

  // 세트상품 판별
  $is_set = !empty($set['rows'][0]['wr_set_sku']);
  $is_combined_shipping = substr_count($wr_ori_order_num_list, $ori_id) > 1;

  $first_row = $set['rows'][0];

  if ($is_set) {
    // 세트상품일 때는 첫 번째 상품의 매출만 사용
    $exchange_rate = (float)$first_row['wr_exchange_rate'];
    $summary['sales'] = (float)$first_row['wr_singo'] * $exchange_rate;
  }

  if ($is_combined_shipping) {
    # 합배송일 때는 첫 번째 상품의 배송비만 사용
    $summary['delivery1'] = $first_row['wr_delivery_fee'];
    $summary['delivery2'] = $first_row['wr_delivery_fee2'];
  }

  # pm_price = 손익 = (매출 * 환율) - (수수료1 + 수수료2 + 기본배송비 + 추가배송비 + 매입원가) - tax 비용 + 부가세 환급
  $pm_price = floor(
    $summary['sales']
    - ($summary['fee'] + $summary['delivery1'] + $summary['delivery2'] + $summary['ibgo'])
    - $summary['tax']
    + $summary['buga']
  );
  $pm_rate = $summary['sales'] > 0 ? floor($pm_price / $summary['sales'] * 10000) / 100 : 0;

  foreach ($set['rows'] as $row) {
    $is_set = !empty($row['wr_set_sku']);
    $group = $is_set ? '세트상품' : ($is_combined_shipping ? '합배송' : '단일배송');

    $exchange_rate = (float)$row['wr_exchange_rate'];
    $wr_singo_rate = (float)$row['wr_singo'] * $exchange_rate;

    switch ($row['wr_paymethod']) {
      case "Shopify Payments":
      case "Shop Cash":
      case "Shop Cash + Shopify Payments":
        $basic_fee = $row['wr_singo'] * 0.024 + 0.3;
        break;
      case "PayPal Express Checkout":
      case "Shopify Payments + PayPal Express Checkout":
        $basic_fee = $row['wr_singo'] * 0.0349 + 0.49;
        break;
      default:
        $basic_fee = 0;
    }

    $basic_fee_rate = $basic_fee * $exchange_rate;
    $wr_delivery_fee = $row['wr_delivery_fee'];
    $wr_delivery_fee2 = $row['wr_delivery_fee2'];
    $ibgo_price = $row['danga'] * $row['chul_ea'];
    $buga_price = $ibgo_price * 0.1;
    $tax_price = floor($row['wr_tax'] * $exchange_rate);

    $display_danga = $is_set ? '' : $row['wr_danga'];
    $display_singo = $is_set ? '' : $row['wr_singo'];

    if ($row['is_duplicate_det']) {
      $display_singo = '';
      $wr_singo_rate = '';
    }

    echo "<tr>",
    "<td>{$group}</td>",
    "<td>{$row['wr_date4']}</td>",
    "<td>{$row['wr_order_num']}<br>{$row['wr_ori_order_num']}</td>",
    "<td>{$row['wr_subject']}</td>",
    "<td>{$row['sku']}</td>",
    "<td>{$row['p_code']}</td>",
    "<td>{$row['wr_currency']}</td>",
    "<td></td>",  // 수수료1 출력 안함
    "<td>0</td>",
    "<td></td>",  // 기본배송비 출력 안함
    "<td></td>",  // 추가배송비 출력 안함
    "<td>{$row['danga']}</td>",
    "<td>{$row['chul_ea']}</td>",
    "<td>{$ibgo_price}</td>", # 합계
    "<td>{$row['wr_exchange_rate']}</td>",
    "<td>{$display_danga}</td>", # 매출 단가,
    "<td>{$display_singo}</td>", # 신고 가격
      "<td>" . number_format($wr_singo_rate) . "</td>",
    "<td></td>", # tax_u
    "<td></td>",  // TAX는 세트 합계에서만 출력
    "<td></td>",
    "<td></td>",  // 손익은 세트 합계에서만 출력
    "<td></td>",  // 이익률도 세트 합계에서만 출력
      "<td>" . number_format($buga_price) . "</td>",
    "</tr>";
  }

  echo "<tr style='background:#ffff99;font-weight:bold'>",
  "<td colspan='7'>{$group} 합계 ({$ori_id})</td>",
    "<td>" . number_format($summary['fee']) . "</td>",
  "<td>0</td>",
    "<td>" . number_format($summary['delivery1']) . "</td>",
    "<td>" . number_format($summary['delivery2']) . "</td>",
  "<td></td>",
  "<td>{$summary['ea']}</td>",
    "<td>" . number_format($summary['ibgo']) . "</td>",
  "<td></td>",
    "<td>" . number_format($summary['danga'], 2) . "</td>",
    "<td>" . number_format($summary['singo'], 2) . "</td>",

    "<td>" . number_format($summary['sales']) . "</td>",
    "<td>" . number_format($summary['tax_u'], 2) . "</td>",
    "<td>" . number_format($summary['tax']) . "</td>",
    "<td>" . number_format($summary['wr_shipping_price'], 2) . "</td>",
    "<td>" . number_format($pm_price) . "</td>",
  "<td>{$pm_rate}</td>",
    "<td>" . number_format($summary['buga']) . "</td>",
  "</tr>";

  foreach ($total as $k => $v) {
    if (isset($summary[$k])) {
      $total[$k] += $summary[$k];
    }
  }
  $total['pm'] += $pm_price;
}

$pm_rate_total = $total['sales'] > 0 ? floor($total['pm'] / $total['sales'] * 10000) / 100 : 0;
echo "<tr style='background:#dff0d8;font-weight:bold'>",
"<td colspan='7'>전체 합계</td>",
  "<td>" . number_format($total['fee']) . "</td>",
"<td>0</td>",
  "<td>" . number_format($total['delivery1']) . "</td>",
  "<td>" . number_format($total['delivery2']) . "</td>",
"<td></td>",
"<td>{$total['ea']}</td>",
  "<td>" . number_format($total['ibgo']) . "</td>",
"<td></td><td></td><td></td>",
  "<td>" . number_format($total['sales']) . "</td>",
  "<td>" . number_format($total['tax_u'], 2) . "</td>",
  "<td>" . number_format($total['tax']) . "</td>",
  "<td>" . number_format($total['wr_shipping_price'], 2) . "</td>",
  "<td>" . number_format($total['pm']) . "</td>",
"<td>{$pm_rate_total}</td>",
  "<td>" . number_format($total['buga']) . "</td>",
"</tr>";
?>


<script type="text/javascript">
  $(document).ready(function () {
  });

</script>
