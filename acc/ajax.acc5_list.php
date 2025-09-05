<?php
include_once("./_common.php");

# 파라메터 받기
$wr_domain = $wr_18;
$wmb_id = $wmb_id;
$st_date = $st_date;
$ed_date = $ed_date;


$sql_where = "";

if ($wr_domain != "") {
  $sql_where .= " AND A.wr_domain = '" . $wr_domain . "' ";
}

if ($ordernum) {
  $ordernum = trim($ordernum);
  $sql_where .= " AND A.wr_order_num = '$ordernum' ";
}

# 검색 기간의 환율 정보 가져오기
$exData = array();
$today = date("Y-m-d");
$date_arr = date_to_date($st_date, $ed_date);

foreach ($date_arr as $k => $v) {
  $ex_date = $v['date'];
  $rowData = array();

  // 검색일자와 오늘날짜가 같은때
  if ($today == $ex_date) {
    $sql = "SELECT * FROM g5_excharge";
    $exRs = sql_query($sql);
    while ($exRow = sql_fetch_array($exRs)) {
      $currency = $exRow['ex_eng'];
      if ($currency == "JPY") {
        $exRow['rate'] = str_replace(",", "", $exRow['rate']) * 0.01;
      }
      $rowData[$currency] = $exRow;
    }
  } else {
    $month = date("Ym", strtotime($ex_date));
    $table = "g5_excharge_log";
    $sql = "SELECT * FROM " . $table . " WHERE ex_date='" . $ex_date . "'";
    $exRs = sql_query($sql);
    $cnt_chk = sql_num_rows($exRs);

    if ($cnt_chk > 0) {
      while ($exRow = sql_fetch_array($exRs)) {
        $currency = $exRow['ex_eng'];
        if ($currency == "JPY") {
          $exRow['rate'] = str_replace(",", "", $exRow['rate']) * 0.01;
        }
        $rowData[$currency] = $exRow;
      }
    } else {
      $after_month = date("Ym", strtotime($ex_date . "+1 month"));
      $after_table = "g5_excharge_log";
      $sql = "SELECT * FROM " . $table . " WHERE ex_date='" . $ex_date . "'";
      $exRs = sql_query($sql);
      $cnt_chk = sql_num_rows($exRs);

      if ($cnt_chk > 0) {
        while ($exRow = sql_fetch_array($exRs)) {
          $currency = $exRow['ex_eng'];
          if ($currency == "JPY") {
            $exRow['rate'] = str_replace(",", "", $exRow['rate']) * 0.01;
          }
          $rowData[$currency] = $exRow;
        }
      } else {
        $sql = "SELECT * FROM g5_excharge";
        $exRs = sql_query($sql);
        while ($exRow = sql_fetch_array($exRs)) {
          $currency = $exRow['ex_eng'];
          if ($currency == "JPY") {
            $exRow['rate'] = str_replace(",", "", $exRow['rate']) * 0.01;
          }
          $rowData[$currency] = $exRow;
        }
      }
    }
  }

  $exData[$ex_date] = $rowData;
}

# 매출 데이터(출고기준) 조회
$sql = "SELECT  
    A.wr_date4,
    A.wr_danga,
    A.wr_paymethod,
    A.wr_domain,
    A.wr_currency,
    C.wr_1 AS 'sku',
    C.wr_5 AS 'p_code',
    IF(A.wr_set_sku != '', A.wr_set_sku, A.wr_order_num) AS 'set',
    A.wr_ori_order_num,
    A.wr_singo, 
    REPLACE(IF(A.wr_exchange_rate = '' OR A.wr_exchange_rate = 0, E.rate, A.wr_exchange_rate), ',', '') AS wr_exchange_rate,
    A.wr_order_num,
    IFNULL(B.ibgo_danga, C.wr_22) AS danga,
    A.wr_tax,
    A.wr_shipping_price,
    A.wr_currency,
    A.wr_delivery_fee,
    A.wr_delivery_fee2,
    A.wr_delivery_oil,
    C.wr_subject,
    IFNULL(B.chul_ea, A.wr_ea) AS chul_ea 
FROM g5_sales3_list A
LEFT OUTER JOIN g5_sales3_det B ON B.sales3_id = A.seq AND B.del_yn = 'N'
LEFT OUTER JOIN g5_write_product C ON C.wr_id = A.wr_product_id
LEFT OUTER JOIN g5_write_product_fee D ON D.wr_id = C.wr_id AND D.warehouse = A.wr_warehouse
LEFT OUTER JOIN g5_excharge E ON E.ex_eng = A.wr_currency
WHERE A.wr_release_use = '1' " . $sql_where . " AND A.wr_date4 BETWEEN '" . $st_date . "' AND '" . $ed_date . "' 
ORDER BY A.wr_domain, A.wr_date4 ASC, A.wr_order_num ASC, A.wr_ori_order_num ASC";

$rs = sql_query($sql);
$list = array();
$wr_ori_order_num_list = "";
$wr_order_num_list = "";

while ($row = sql_fetch_array($rs)) {
  $wr_ori_order_num = $row['wr_ori_order_num'];
  $wr_order_num = $row['wr_order_num'];
  $set = $row['set'];
  $list[$wr_ori_order_num]['data'][$set]['data'][$wr_order_num]['data'][] = $row;

  $wr_ori_order_num_list = $wr_ori_order_num_list . "," . $wr_ori_order_num;
  $wr_order_num_list = $wr_order_num_list . "," . $wr_order_num;
}

foreach ($list as $k => $v) {
  foreach ($v['data'] as $k2 => $v2) {
    foreach ($v2['data'] as $k3 => $v3) {
      $cnt = count($list[$k]['data'][$k2]['data'][$k3]['data']);
      $list[$k]['data'][$k2]['data'][$k3]['cnt'] = (int)$list[$k]['data'][$k2]['data'][$k3]['cnt'] + $cnt;
      $list[$k]['data'][$k2]['cnt'] = (int)$list[$k]['data'][$k2]['cnt'] + $cnt;
      $list[$k]['cnt'] = (int)$list[$k]['cnt'] + $cnt;
    }
  }
}
?>

<?php
if ($st_date && $ed_date) {
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

  // 부가세환급
  $total_buga_price = 0;
  $set_sales_price = 0;
  $sales3_det_num_count = 1;

  $result = sql_fetch_all($sql);
  $chknum = 1;

  for ($i = 0; $i < count($result); $i++) {
    $row = $result[$i];
    $ibgo_price = 0;

    # 기본수수료 계산
    if ($row['wr_paymethod'] == "Shopify Payments") {
      $basic_fee = $row['wr_singo'] * 2.4 / 100 + 0.3;
    } else if ($row['wr_paymethod'] == "PayPal Express Checkout") {
      $basic_fee = $row['wr_singo'] * 3.49 / 100 + 0.49;
    } else if ($row['wr_paymethod'] == "Shop Cash") {
      $basic_fee = $row['wr_singo'] * 2.4 / 100 + 0.3;
    } else if ($row['wr_paymethod'] == "Shopify Payments + PayPal Express Checkout") {
      $basic_fee = $row['wr_singo'] * 3.49 / 100 + 0.49;
    } else if ($row['wr_paymethod'] == "Shop Cash + Shopify Payments") {
      $basic_fee = $row['wr_singo'] * 2.4 / 100 + 0.3;
    } else {
      $basic_fee = 0;
    }

    // 환율
    $exchange_rate = $row['wr_currency'] === 'JPY' ? $row['wr_exchange_rate'] / 100 : $row['wr_exchange_rate'];
    $wr_ori_order_num_val = $row['wr_ori_order_num'];

    // 합배송 or 셋트 갯수체크(1개 이상이면 합배송 및 셋트)
    $acc5_set_count = substr_count($wr_ori_order_num_list, $wr_ori_order_num_val);

    // wr_order_num 중복값 체크
    $acc5_wr_order_num_count = substr_count($wr_order_num_list, $row['wr_order_num']);

    // g5_sales3_det 테이블에 중복 데이터가 있을경우 계산 0
    if ($sales3_det_num_count < $acc5_wr_order_num_count) {
      $wr_singo_rate = 0; // 매출(신고가격) * 환율
    } else {
      $wr_singo_rate = (((float)$row['wr_singo']) * $exchange_rate);
    }

    if ($sales3_det_num_count < $acc5_wr_order_num_count) {
      $basic_fee_rate = 0; // 수수료1
    } else {
      $basic_fee_rate = $basic_fee * $exchange_rate;
    }

    # 수수료2의 경우 도도스킨(자사몰)의 경우 수수료2가 0원
    if ($sales3_det_num_count < $acc5_wr_order_num_count) {
      if ($row['wr_domain'] == "dodoskin") {
        $sales_fee_rate = 0;
      } else {
        $sales_fee_rate = 0;
      }
    } else {
      $sales_fee_rate = 0;
    }

    // 기본배송비 / 추가배송비
    if ($sales3_det_num_count < $acc5_wr_order_num_count) {
      $wr_delivery_fee = 0;
      $wr_delivery_fee2 = 0;
    } else {
      $wr_delivery_fee = $row['wr_delivery_fee'];
      $wr_delivery_fee2 = $row['wr_delivery_fee2'];
    }

    // 합계
    $total_sales_price = $total_sales_price + ($wr_singo_rate); // 매출* 환율
    $total_basic_fee = $total_basic_fee + ($basic_fee_rate); // 수수료1
    $total_sales_fee = $total_sales_fee + ($sales_fee_rate * $exchange_rate); // 수수료2
    $total_delivery_fee = $total_delivery_fee + $wr_delivery_fee; // 기본배송비
    $total_delivery_fee2 = $total_delivery_fee2 + $wr_delivery_fee2; // 추가배송비

    if ($acc5_set_count == $chknum) {
      $total_tax = $total_tax + floor(((float)$row['wr_tax']) * $exchange_rate);
    }

    $ibgo_price = $ibgo_price + ($row['danga'] * $row['chul_ea']);
    $total_ibgo_price = $total_ibgo_price + ($row['danga'] * $row['chul_ea']);

    // 부가세환급
    $buga_price = ($row['danga'] * $row['chul_ea']) * 0.1;

    // 부가세환급 합계
    $total_buga_price = $total_buga_price + ($row['danga'] * $row['chul_ea']) * 0.1;

    // 매출 * 환율(세트합산)
    $set_sales_price = $set_sales_price + $wr_singo_rate;

    // 부가세환급(세트합산)
    $set_buga_price = $set_buga_price + $buga_price;

    // TAX(세트별로 1개씩만 계산)
    if ($acc5_set_count == $chknum) {
      $tax_price = floor(((float)$row['wr_tax']) * $exchange_rate);
      $set_tax_price = $set_tax_price + $tax_price;
    }

    // 수수료1(세트합산)
    $basic_fee_price = floor($basic_fee_rate);
    $set_basic_fee_price = $set_basic_fee_price + $basic_fee_price;

    // 기본배송비/추가배송비
    $set_delivery1_price = $set_delivery1_price + $wr_delivery_fee;
    $set_delivery2_price = $set_delivery2_price + $wr_delivery_fee2;

    // 합계(세트합산)
    $set_total_price = $set_total_price + ($row['danga'] * $row['chul_ea']);

    $pm_price = ($set_sales_price + $set_buga_price - $set_tax_price) - ($set_basic_fee_price + $set_delivery1_price + $set_delivery2_price + $set_total_price);
    $pm_price = floor($pm_price);

    // 이익률(%) = 손익 / 매출 * 환율 * 100
    if ($acc5_set_count == $chknum) {
      $pm_rate = floor($pm_price) / $set_sales_price * 100;
    }

    $total_ea = $total_ea + $row['chul_ea'];
    $wr_ori_order_num = $row['wr_ori_order_num'];

    if ($i % 2 == 0) {
      //$b_color="#e6e6e6";
    } else {
      //$b_color="#ffffff";
    }
    ?>
    <tr>
      <td style="background-color: <?= $b_color ?>;"><?= $row['wr_domain'] ?></td>
      <td><?= $row['wr_date4'] ?></td>

      <td>
        <?= $row['wr_order_num'] ?>
        <br><?= $row['wr_ori_order_num'] ?>
      </td>

      <td><?= $row['wr_subject'] ?></td>
      <td><?= $row['sku'] ?></td>
      <td><?= $row['p_code'] ?></td>
      <td><?= $row['wr_currency'] ?></td>

      <!-- 수수료1/수수료2 -->
      <td><?= number_format($basic_fee_rate) ?></td>
      <td><?= number_format($sales_fee_rate) ?></td>

      <!-- 기본 배송비 / 추가 배송비 -->
      <td><?= number_format($wr_delivery_fee) ?></td>
      <td><?= number_format($wr_delivery_fee2) ?></td>

      <!-- 매입원가/	수량/	합계/	환율 -->
      <td><?= number_format($row['danga']) ?></td>
      <td><?= number_format($row['chul_ea']) ?></td>
      <td><?= number_format($row['danga'] * $row['chul_ea']) ?></td>
      <td><?= number_format($row['wr_exchange_rate']) ?></td>

      <!-- 매출단가	 / 신고가격	 -->
      <td><?= number_format((float)$row['wr_danga']) ?></td>
      <td><?= number_format((float)$row['wr_singo']) ?></td>

      <!-- 매출 * 환율(원) -->
      <td>
        <?php if ($sales3_det_num_count < $acc5_wr_order_num_count) { ?>
        <?php } else { ?>
          <?= number_format($wr_singo_rate) ?>
        <?php } ?>
      </td>

      <!-- TAX -->
      <?php if ($acc5_set_count == $chknum) { ?>
        <td><?= number_format(floor(((float)$row['wr_tax']) * $exchange_rate)) ?></td>
      <?php } else { ?>
        <td style="border-bottom: 0px solid #d9dee9;"></td>
      <?php } ?>

      <!-- 손익(원) -->
      <?php if ($acc5_set_count == $chknum) { ?>
        <td><?= number_format($pm_price) ?></td>
      <?php } else { ?>
        <td style="border-bottom: 0px solid #d9dee9;"></td>
      <?php } ?>

      <!-- 이익률 손익 / 매출 * 환율 * 100 -->
      <?php if ($acc5_set_count == $chknum) { ?>
        <td><?= sprintf('%0.2f', $pm_rate) ?></td>
      <?php } else { ?>
        <td style="border-bottom: 0px solid #d9dee9;"></td>
      <?php } ?>

      <!-- 부가세환급 -->
      <td><?= number_format($buga_price) ?></td>
    </tr>
    <?php

    if ($acc5_set_count == $chknum) {
      $total_pm_price = $total_pm_price + $pm_price;
      $total_rate = floor($total_pm_price) / $total_sales_price * 100;
      $total_rate = floor($total_rate);

      $set_sales_price = 0;
      $set_buga_price = 0;
      $set_tax_price = 0;
      $set_basic_fee_price = 0;
      $set_delivery1_price = 0;
      $set_delivery2_price = 0;
      $set_total_price = 0;
    }

    // 셋트 갯수랑 중가값이 같다면 1개세트끝 다시 chknum 초기화
    if ($acc5_set_count == $chknum) {
      $chknum = 1;
    } else {
      $chknum++;
    }

    // g5_sales3_det 테이블에 중복 데이터가 있을경우 마지막 중복데이터빼고 나머지++
    if ($acc5_wr_order_num_count > $sales3_det_num_count) {
      $sales3_det_num_count++;
    } else {
      $sales3_det_num_count = 1; // 초기화
    }
  }
  ?>

  <!-- 합계 -->
  <tr>
    <td colspan="7">합계</td>
    <td><?= number_format($total_basic_fee) ?></td>
    <td><?= number_format($total_sales_fee) ?></td>
    <td><?= number_format($total_delivery_fee) ?></td>
    <td><?= number_format($total_delivery_fee2) ?></td>
    <td></td>
    <td><?= number_format($total_ea) ?></td>
    <td><?= number_format($total_ibgo_price) ?></td>
    <td></td>
    <td></td>
    <td></td>

    <!-- 매출 * 환율 -->
    <td><?= number_format($total_sales_price) ?></td>

    <!-- tax -->
    <td><?= number_format($total_tax) ?></td>

    <td><?= number_format($total_pm_price) ?></td>

    <td><?= $total_rate ?></td>

    <td><?= number_format($total_buga_price) ?></td>
  </tr>
  <!-- //합계 -->
<?php } else { ?>
  <tr>
    <td colspan="9">도메인과 검색일자를 선택해주세요.</td>
  </tr>
<?php } ?>

<script type="text/javascript">
  $(document).ready(function() {
    //$(".tooltip_event").tooltip();
  });
</script>