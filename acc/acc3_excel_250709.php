<?php
include_once('../common.php');

if ($is_guest) {
  alert('로그인 후 이용바랍니다.');
}

$filename = "매출처원장_" . G5_TIME_YMD . ".xls";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . $filename);
header("Content-Description: PHP4 Generated Data");

$EXCEL_FILE = "
				<table border='1'>

					<tr style=\"background:#ddd\">
						<th style=\"width:150px\">매출처</th>
						<th style=\"width:100px\">일자</th>
						<th style=\"width:200px\">주문번호</th>
						<th style=\"width:200px\">대표코드</th>
						<th style=\"width:200px\">SKU</th>
						<th style=\"width:200px\">상품명</th>
						<th style=\"width:200px\">수량</th>
						<th style=\"width:200px\">단가($)</th>
						<th style=\"width:200px\">신고가격($)</th>
						<th style=\"width:200px\">수수료1</th>
						<th style=\"width:200px\">수수료2</th>
						<th style=\"width:200px\">TAX</th>
						<th style=\"width:200px\">잔액</th>
						<th style=\"width:200px\">정산 유무</th>
					</tr>
			";


if (!$st_date) {
  $st_date = date("Y-m-d");
}
if (!$ed_date) {
  $ed_date = date("Y-m-d");
}

$search_accumulate = 'N';

$carryover = 0;
$accumulate_amount = 0;

# 환율 정보
$sql = "select rate, ex_eng from g5_excharge";
$result = sql_fetch_all($sql);

$ex_list = array_column($result, 'rate', 'ex_eng');

# 이월 합산 금액 검색
if ($sc_domain) {
  # 이월 합산 금액 보정
  $sql = "SELECT sum(amount) as amount FROM g5_acc3_carryover_amount WHERE sc_domain = '{$sc_domain}' and base_date <= '{$st_date}'";
  $result = sql_fetch($sql);

  $carryover = $result['amount'];


  $search_accumulate = 'Y';

  if ($sc_domain === 'ACSUM') {
    $query = "
    select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num, wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain IN ('AC', 'ACF')
        order by wr_date4 desc, wr_domain, wr_order_num desc
    ";

  } else if ($sc_domain === 'ACDSUM') {
    $query = "
    select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num, wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD')
        order by wr_date4 desc, wr_domain, wr_order_num desc
    ";
  } else if ($sc_domain === 'Q10JPSUM') {
    $query = "
    select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num, wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain IN ('qoo10-jp', 'qoo10jp-1')
        order by wr_date4 desc, wr_domain, wr_order_num desc
    ";
  } else {
    $query = "
    select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num, wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain = '$sc_domain'
        order by wr_date4 desc, wr_domain, wr_order_num desc
    ";
  }

  $result = sql_query($query);

  while ($row = sql_fetch_array($result)) {
    $list0[$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
  }

  foreach ($list0 as $k => $v) {
    foreach ($v as $k2 => $v2) {
      foreach ($v2 as $k3 => $item) {
        // 환율
        if ($item['wr_currency'] === 'JPY') {
          $item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
        }

        $exchange_rate = $item['wr_exchange_rate'];
        // 수수료1 계산
        $basic_fee_rate = calculateBasicFee(
          $item['wr_paymethod'],
          $item['wr_singo'],
          $exchange_rate,
          false
        );

        // 수수료2 계산
        $sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

        // 세트의 마지막 여부 확인
        $isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list0);

        // TAX 계산
        $tax_price = calculateTax(
          $item['wr_tax'],
          $item['wr_shipping_price'],
          $exchange_rate,
          $isFinal
        );

        $remainingAmount = $item['wr_singo'] * $exchange_rate;

        if ($item['fee1_status']) {
          $remainingAmount -= $basic_fee_rate;
        }

        if ($item['fee2_status']) {
          $remainingAmount -= $sales_fee_rate;
        }

        if ($item['tax_status']) {
          $remainingAmount -= $tax_price;
        }

        $remainingAmount = $remainingAmount < 0 ? 0 : $remainingAmount;

        $accumulate_amount += $remainingAmount;
      }
    }
  }

}

if (!$search_by_date) {
  $search_by_date = 'N';
}

$sql_where = "";
if ($sc_domain === 'ACSUM') {
  $sql_where .= " and wr_domain IN ('AC', 'ACF') ";
} else if ($sc_domain == 'ACDSUM') {
  $sql_where .= " and wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD') ";
} else if ($sc_domain == 'Q10JPSUM') {
  $sql_where .= " and wr_domain IN ('qoo10-jp', 'qoo10jp-1') ";
} else if ($sc_domain) {
  $sql_where .= " AND wr_domain = '" . $sc_domain . "' ";
}

if ($sc_cal_chk) {
  $sql_where .= " AND wr_cal_chk = '" . $sc_cal_chk . "' ";
}

if ($search_value) {
  $search_value = trim($search_value);
  $sql_where .= " AND (wr_order_num = '$search_value' or IF(wr_product_nm = '' OR wr_product_nm IS NULL, wr_subject, wr_product_nm) LIKE '%$search_value%')";
}

$list = [];

$query = "
    select wr_date4, seq, if(wr_exchange_rate > 0, wr_exchange_rate, e.rate) as wr_exchange_rate, wr_domain, wr_order_num, wr_ori_order_num, IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) as code, wr_1, IF(wr_product_nm = '' OR wr_product_nm IS NULL, wr_subject, wr_product_nm) as product_nm, wr_ea, wr_danga, wr_singo, wr_cal_chk, wr_paymethod, wr_singo, wr_currency, wr_tax, wr_shipping_price, sc.*
    from g5_sales3_list as sl
    left join g5_write_product wp on wp.wr_id = sl.wr_product_id
		left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
    left join g5_excharge e on e.ex_eng = sl.wr_currency
    where wr_release_use = '1' {$sql_where} and wr_date4 between '" . $st_date . "' and '" . $ed_date . "'
    order by wr_date4 desc, wr_domain, wr_order_num desc
";

$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
  if ($search_by_date === 'Y') {
    $list[$row['wr_date4']][$row['wr_domain']][$row['seq']] = $row;
  } else {
    $list[$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
  }
}


// 수수료 계산 함수
function calculateBasicFee($paymentMethod, $wrSingo, $exchangeRate, $isDuplicate)
{
  // 중복 데이터라면 수수료 0 반환
  if ($isDuplicate) {
    return 0;
  }

  // 결제 수단에 따른 기본 수수료 계산
  switch ($paymentMethod) {
    case "Shopify Payments":
    case "Shop Cash":
    case "Shop Cash + Shopify Payments":
      $basicFee = $wrSingo * 2.4 / 100 + 0.3;
      break;

    case "PayPal Express Checkout":
    case "Shopify Payments + PayPal Express Checkout":
      $basicFee = $wrSingo * 3.49 / 100 + 0.49;
      break;

    default:
      $basicFee = 0; // 기타 결제 수단의 경우
      break;
  }

  // 환율 적용
  return $basicFee * $exchangeRate;
}

// 수수료2 계산 함수 (예: 도도스킨 등 추가 계산 방식 포함 가능)
function calculateSalesFee($domain, $isDuplicate)
{
  if ($isDuplicate) {
    return 0;
  }

  if ($domain === "dodoskin") {
    return 0; // 도도스킨의 경우 현재 수수료2는 0으로 처리
  }

  // 기타 계산 방식 (필요 시 추가)
  return 0;
}

// TAX 계산 함수
function calculateTax($taxAmount, $shippingPrice, $exchangeRate, $isFinalSet = true)
{
  // 세트의 마지막 데이터만 TAX 계산
  if (!$isFinalSet) {
    return 0;
  }

  // TAX 계산: (세금 + 배송비) × 환율
  $tax = ((float)$taxAmount + (float)$shippingPrice) * (float)$exchangeRate;

  return floor($tax); // 소수점 절사
}

// 세트의 마지막 여부 확인 함수
function isFinalSet($currentSeq, $orderNum, $list)
{
  $itemSequences = []; // 해당 주문번호의 모든 seq를 저장할 배열

  // 세트 내 해당 주문번호의 모든 seq 수집
  foreach ($list as $domain => $dateGroup) {
    foreach ($dateGroup as $date => $items) {
      foreach ($items as $seq => $item) {
        if ($item['wr_ori_order_num'] === $orderNum) {
          $itemSequences[] = $seq;
        }
      }
    }
  }

  // seq 정렬 (숫자 순서 보장)
  sort($itemSequences);

  // 현재 seq가 해당 주문번호의 마지막 seq인지 확인
  return $currentSeq === end($itemSequences);
}

$total_ea = 0;
$total_danga = 0;
$total_singo = 0;
$total_fee1 = 0;
$total_fee2 = 0;
$total_tax = 0;
$total_remainingAmount = 0;
$tr_index = 0;

foreach ($list as $k => $v) {
  $domain = $k;
  foreach ($v as $k2 => $v2) {
    $date = $k2;
    $rowspan = count($v2);
    $row1 = true;
    $rowspan2 = count($v2);
    $row2 = true;

    $tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
    foreach ($v2 as $k3 => $item) {
      $total_ea += $item['wr_ea'];
      $total_danga += $item['wr_danga'];
      $total_singo += $item['wr_singo'];

      // 환율
      if ($item['wr_currency'] === 'JPY') {
        $item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
      }
      $exchange_rate = $item['wr_exchange_rate'];
      // 수수료1 계산
      $basic_fee_rate = calculateBasicFee(
        $item['wr_paymethod'],
        $item['wr_singo'],
        $exchange_rate,
        false
      );

      // 수수료2 계산
      $sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

      // 세트의 마지막 여부 확인
      $isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list);

      // TAX 계산
      $tax_price = calculateTax(
        $item['wr_tax'],
        $item['wr_shipping_price'],
        $exchange_rate,
        $isFinal
      );

      $remainingAmount = $item['wr_singo'] * $exchange_rate;

      if ($item['fee1_status']) {
        $remainingAmount -= $basic_fee_rate;
      }

      if ($item['fee2_status']) {
        $remainingAmount -= $sales_fee_rate;
      }

      if ($item['tax_status']) {
        $remainingAmount -= $tax_price;
      }


      $total_fee1 += $basic_fee_rate;
      $total_fee2 += $sales_fee_rate;
      $total_tax += $tax_price;
      $total_remainingAmount += $remainingAmount;


      $EXCEL_FILE .= "
      <tr>
        <td style=mso-number-format:'\@'>" . $domain . "</td>
        <td style=mso-number-format:'\@'>" . $date . "</td>
        <td style=mso-number-format:'\@'>" . $item['wr_order_num'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['code'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['wr_1'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['product_nm'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['wr_ea'] . "</td>
        <td style=mso-number-format:'\@'>" . number_format($item['wr_danga'], 2) . "</td>
        <td style=mso-number-format:'\@'>" . number_format($item['wr_singo'], 2) . "</td>
        <td style=mso-number-format:'\@'>" . number_format($basic_fee_rate) . "</td>
        <td style=mso-number-format:'\@'>" . number_format($sales_fee_rate) . "</td>
        <td style=mso-number-format:'\@'>" . number_format($tax_price) . "</td>
        <td style=mso-number-format:'\@'>" . number_format($remainingAmount) . "</td>
        <td style=mso-number-format:'\@'>" . ($item['wr_cal_chk'] === 'Y' ? '정산완료' : '미정산') . "</td>
      </tr>
      ";
    }
  }
}


$EXCEL_FILE .= "</table>";

echo "
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;