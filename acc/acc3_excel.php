<?php
include_once('../common.php');

if ($is_guest) {
  alert('로그인 후 이용바랍니다.');
}

if (!$st_date) {
  $st_date = date("Y-m-d");
}
if (!$ed_date) {
  $ed_date = date("Y-m-d");
}

$search_accumulate = 'N';

$carryover = 0;
$accumulate_amount = 0;

$sql = "select rate, ex_eng from g5_excharge";
$result = sql_fetch_all($sql);

$ex_list = array_column($result, 'rate', 'ex_eng');


if (!$search_by_date) {
  $search_by_date = 'N';
}

$sql_where = "";

if ($sc_domain === 'ACSUM') {
  $sql_where .= " and wr_domain IN ('AC', 'ACF') ";
} elseif ($sc_domain == 'ACDSUM') {
  $sql_where .= " and wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD') ";
} elseif ($sc_domain == 'Q10JPSUM') {
  $sql_where .= " and wr_domain IN ('qoo10-jp', 'qoo10jp-1') ";
} elseif ($sc_domain) {
  $sql_where .= " AND wr_domain = '" . $sc_domain . "' ";
}

if ($sc_cal_chk) {
  $sql_where .= " AND wr_cal_chk = '" . $sc_cal_chk . "' ";
}

if ($search_value) {
  $search_value = trim($search_value);
  $sql_where .= " AND (wr_order_num = '$search_value' or IF(wr_product_nm = '' OR wr_product_nm IS NULL, 
                    wr_subject, wr_product_nm) LIKE '%$search_value%')";
}

$list = [];

if ($ledger === 'all' || $ledger === 'account' || !isset($ledger)) {
  $query = "
    select wr_date4, seq, if(wr_exchange_rate > 0, wr_exchange_rate, e.rate) as wr_exchange_rate, 
    wr_domain, wr_order_num, wr_ori_order_num, IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) as code, 
    wr_1, IF(wr_product_nm = '' OR wr_product_nm IS NULL, wr_subject, wr_product_nm) as product_nm, wr_ea, 
    wr_danga, wr_singo, wr_cal_chk, wr_paymethod, wr_singo, wr_currency, wr_tax, wr_shipping_price, sc.*
    from g5_sales3_list as sl
    left join g5_write_product wp on wp.wr_id = sl.wr_product_id
	left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
    left join g5_excharge e on e.ex_eng = sl.wr_currency
    where wr_release_use = '1' {$sql_where} and wr_date4 between '" . $st_date . "' and '" . $ed_date . "'
    order by wr_date4 desc, wr_domain, wr_order_num desc
    ";

  $result = sql_query($query);

  if (sql_num_rows($result) > 0) {
    while ($row = sql_fetch_array($result)) {
      $row['receipt'] = 'N';

      if ($search_by_date === 'Y') {
        $list[$row['receipt']][$row['wr_date4']][$row['wr_domain']][$row['seq']] = $row;
      } else {
        $list[$row['receipt']][$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
      }
    }
  }
}

if ($ledger === 'all' || $ledger === 'receipt' || !isset($ledger)) {
  $sql_search = " ";

  if ($st_date && $ed_date) {
    $sql_search .= " and l.wr_datetime BETWEEN '{$st_date} 00:00:00' AND '{$ed_date} 23:59:59' ";
  }

  $sql_search .= " and l.wr_state = 2 ";

  if ($stx) {
    $sql_search .= " and l.wr_order_num LIKE '%$stx%' ";
  }

  if (isset($sc_domain) && isNotEmpty($sc_domain)) {
    $sql_search .= $sql_where;
  }

  if (!$sst && !$sod) {
    $sst = "l.seq";
    $sod = "desc";
  }

  $sql_order = "order by $sst $sod";

  $query = "
    SELECT
        sl.wr_date4, sl.seq, sl.wr_domain, sl.wr_order_num, sl.wr_ori_order_num,
        wp.wr_1, sl.wr_ea, sl.wr_danga, sl.wr_singo, sl.wr_cal_chk,
        sl.wr_paymethod, sl.wr_currency, sl.wr_tax, sl.wr_shipping_price,
        IF(sl.wr_exchange_rate > 0, sl.wr_exchange_rate, e.rate) AS wr_exchange_rate,
        IFNULL(wp.wr_5, IFNULL(wp.wr_4, IFNULL(wp.wr_6, ''))) AS code,
        IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wp.wr_subject, sl.wr_product_nm) AS product_nm,
        sc.*
    FROM
        g5_sales3_list AS sl
    LEFT JOIN
        g5_write_product wp ON wp.wr_id = sl.wr_product_id
    LEFT JOIN
        g5_sales3_cal sc ON sc.sales3_seq = sl.seq
    LEFT JOIN
        g5_excharge e ON e.ex_eng = sl.wr_currency
    WHERE
        sl.wr_release_use = '1'
        AND sl.wr_order_num IN (
        SELECT 
            l.wr_order_num
            FROM g5_return_list as l
            LEFT JOIN g5_sales3_list as s3 ON s3.seq = l.sales3_id
            WHERE (1) {$sql_search} {$sql_order} )           
    ORDER BY
        sl.wr_date4 DESC, sl.wr_domain, sl.wr_order_num DESC;
    ";

  $result = sql_query($query);

  if (sql_num_rows($result) > 0) {
    while ($row = sql_fetch_array($result)) {
      $row['receipt'] = 'Y';
      $row['wr_ea'] = -$row['wr_ea'];
      $row['wr_singo'] = -$row['wr_singo'];

      if ($search_by_date === 'Y') {
        $list[$row['receipt']][$row['wr_date4']][$row['wr_domain']][$row['seq']] = $row;
      } else {
        $list[$row['receipt']][$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
      }
    }
  }
}

$filename = "매출처원장_" . $st_date . "_" . $ed_date . "_" . $sc_domain . ".xls";

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

function calculateBasicFee($paymentMethod, $wrSingo, $exchangeRate, $isDuplicate)
{
  if ($isDuplicate) {
    return 0;
  }

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
      $basicFee = 0;
      break;
  }

  return $basicFee * $exchangeRate;
}

function calculateSalesFee($domain, $isDuplicate)
{
  if ($isDuplicate) {
    return 0;
  }

  if ($domain === "dodoskin") {
    return 0;
  }

  return 0;
}

function calculateTax($taxAmount, $shippingPrice, $exchangeRate, $isFinalSet = true)
{
  if (!$isFinalSet) {
    return 0;
  }

  $tax = ((float)$taxAmount + (float)$shippingPrice) * (float)$exchangeRate;

  return floor($tax);
}

function isFinalSet($currentSeq, $orderNum, $list)
{
  $itemSequences = [];

  foreach ($list as $domain => $dateGroup) {
    foreach ($dateGroup as $date => $items) {
      foreach ($items as $seq => $item) {
        if ($item['wr_ori_order_num'] === $orderNum) {
          $itemSequences[] = $seq;
        }
      }
    }
  }

  sort($itemSequences);

  return $currentSeq === end($itemSequences);
}

if (count($list) <= 0) {
  die("검색된 데이터가 없습니다.");
}
$total_ea = 0;
$total_danga = 0;
$total_singo = 0;
$total_fee1 = 0;
$total_fee2 = 0;
$total_tax = 0;
$total_remainingAmount = 0;
$tr_index = 0;

foreach ($list as $receipt_key => $val_by_receipt) {
  $receipt = $receipt_key;

  foreach ($val_by_receipt as $domain_key => $val_by_domain) {
    $domain = $domain_key;

    foreach ($val_by_domain as $date => $items_for_date) {
      foreach ($items_for_date as $item) {



        if ($item['wr_currency'] === 'JPY') {
          $item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
        }
        $exchange_rate = $item['wr_exchange_rate'];
        $basic_fee_rate = calculateBasicFee(
          $item['wr_paymethod'],
          $item['wr_singo'],
          $exchange_rate,
          false
        );

        $sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

        // $isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list);

        $tax_price = calculateTax(
          $item['wr_tax'],
          $item['wr_shipping_price'],
          $exchange_rate,
          // $isFinal
          true
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

        $excel_number_format = "'#,##0;[red]-#,##0;0'";
        $excel_number_format_float = "'#,##0.00;[red]-#,##0.00;0.00'";
        $EXCEL_FILE .= "
      <tr>
        <td style=mso-number-format:'\@'>" . $domain . "</td>
        <td style=mso-number-format:'\@'>" . $date . "</td>
        <td style=mso-number-format:'\@'>" . $item['wr_order_num'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['code'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['wr_1'] . "</td>
        <td style=mso-number-format:'\@'>" . $item['product_nm'] . "</td>
        <td style=mso-number-format:" . $excel_number_format .">" . htmlspecialchars($item['wr_ea']) . "</td>
        <td style=mso-number-format:" . $excel_number_format_float .">" . htmlspecialchars($item['wr_danga'], 2) . "</td>
        <td style=mso-number-format:" . $excel_number_format_float .">" . htmlspecialchars($item['wr_singo']) . "</td>
        <td style=mso-number-format:" . $excel_number_format .">" . htmlspecialchars($basic_fee_rate) . "</td>
        <td style=mso-number-format:" . $excel_number_format .">" . htmlspecialchars($sales_fee_rate) . "</td>
        <td style=mso-number-format:" . $excel_number_format .">" . htmlspecialchars($tax_price) . "</td>
        <td style=mso-number-format:" . $excel_number_format_float .">" . htmlspecialchars($remainingAmount) . "</td>
        <td style=mso-number-format:'\@'>" . ($item['wr_cal_chk'] === 'Y' ? '정산완료' : '미정산') . "</td>
      </tr>
      ";
      }
    }
  }
}

$EXCEL_FILE .= "</table>";

echo "
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;
