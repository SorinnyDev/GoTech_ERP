<?php
include_once('./_common.php');

// 제품이 많을 경우 대비 설정변경
set_time_limit(0);
ini_set('memory_limit', '50M');

function only_number($n)
{
  return preg_replace('/[^0-9]/', '', (string)$n);
}

if (!isset($member['mb_id'])) {
  alert('잘못된 접근입니다.');
}

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if (!$is_upload_file) {
  alert("엑셀 파일을 업로드해 주세요.");
}

if ($is_upload_file) {
  $file = $_FILES['excelfile']['tmp_name'];

  include_once(G5_LIB_PATH . '/PHPExcel/IOFactory.php');

  $objPHPExcel = PHPExcel_IOFactory::load($file);
  $sheet = $objPHPExcel->getSheet(0);

  $num_rows = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();

  $dup_it_id = array();
  $fail_it_id = array();
  $dup_count = 0;
  $total_count = 0;
  $fail_count = 0;
  $fail_count2 = 0;
  $succ_count = 0;

  # 환율 정보
  $sql = "select rate, ex_eng from g5_excharge";
  $result = sql_fetch_all($sql);

  $ex_list = array_column($result, 'rate', 'ex_eng');

  $ex_jpy = $ex_list['JPY'] / 100;


  for ($i = 2; $i <= $num_rows; $i++) {

    $j = 0;
    $rack_id = '';

    $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
      NULL,
      TRUE,
      FALSE);

    $wr_order_num = addslashes(trim($rowData[0][$j++])); //주문번호
    $wr_release_traking = addslashes(trim($rowData[0][$j++])); //수출트래킹NO
    $wr_delivery_nm = trim($rowData[0][$j++]); // 배송사명
    $wr_delivery_fee = trim($rowData[0][$j++]); // 기본 배송비
    $wr_delivery_fee2 = trim($rowData[0][$j++]); // 추가 배송비

    if (empty($wr_order_num) && empty($wr_release_traking) && empty($wr_delivery_nm) && empty($wr_delivery_fee) && empty($wr_delivery_fee2)) {
      continue;
    }

    $total_count++;


    if (!$wr_order_num) {
      $fail_count++;
      continue;
    }

    $chk = sql_fetch("select * from g5_sales3_list where wr_order_num = '{$wr_order_num}'");

    if (!$chk) {
      $fail_count++;
      continue;
    }

    if ($chk['wr_rack']) {
      $rack_id = $chk['wr_rack'];
    } else {
      $rack = sql_fetch("select wr_rack from g5_rack_stock where wr_warehouse = '{$chk['wr_warehouse']}' and wr_product_id = '{$chk['wr_product_id']}'  GROUP BY wr_rack HAVING SUM(wr_stock) > 0;");
      $rack_id = $rack['wr_rack'];
    }

    $insert_sql = " ";
    if ($wr_release_traking) {
      $insert_sql .= ",wr_release_traking = '{$wr_release_traking}' ";
    }

    if ($wr_delivery_fee2) {
      $wr_delivery_fee2 = (int)$wr_delivery_fee2;
      $insert_sql .= ",wr_delivery_fee2 = '{$wr_delivery_fee2}' ";
    }

    # 배송사 O 배송비 O
    if ($wr_delivery_nm && $wr_delivery_fee) {
      $sql = "select * from g5_delivery_company where wr_name = '$wr_delivery_nm' and wr_use = 1";
      $delivery_data = sql_fetch($sql);

      # 유류할증료는 배송비 총 금액 담아서 계산
      $wr_delivery_fee = (int)$wr_delivery_fee;

      $insert_sql .= ",wr_delivery_fee = '{$wr_delivery_fee}' ";
      $insert_sql .= ",wr_delivery = '{$delivery_data['wr_code']}' ";

    } else if ($wr_delivery_nm) { # 배송사 O
      # 기존에 배송비가 있으면 변경 X
      $sql = "select * from g5_delivery_company where wr_name = '$wr_delivery_nm' and wr_use = 1";
      $delivery_data = sql_fetch($sql);

      if (!$chk['wr_delivery_fee']) {
        $wr_delivery_fee = 0;

        $sql = "select * from g5_sales3_list where wr_order_num LIKE '%{$chk['wr_ori_order_num']}%' AND wr_warehouse='" . $chk['wr_warehouse'] . "' order by wr_order_num asc";
        $hap = sql_query($sql);

        $total_weight = 0;
        $wr_weight3 = 0;
        while ($item2 = sql_fetch_array($hap)) {
          $wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$item2['wr_product_id']}'");

          $total_weight += (float)$wr_weight3['wr_10'] * (int)$item2['wr_ea'];
        }

        // 중량무게 계산
        $weight1 = $chk['wr_weight1'] ?? 0;
        $weight2 = $chk['wr_weight2'] ?? 0;

        // 실제 무게, 부피, 중량무게 중 가장 큰 값 사용
        $max_weight = max($total_weight, $weight1, $weight2);

        // 국가 코드 가져오기
        $country_dcode = sql_fetch("SELECT wr_code as code FROM g5_country WHERE code_2 = '{$chk['wr_deli_country']}'");
        $country = $country_dcode['code'];

        // 배송비 조회
        $sql = "SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name 
					FROM g5_shipping_price A
					LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
					WHERE weight_code >= {$max_weight} 
					AND {$country} != 0 
					and C.wr_use='1' 
          and cust_code in ('{$delivery_data['wr_code']}')
					GROUP BY cust_code 
					ORDER BY price ASC
        ";

        $result = sql_fetch_all($sql);

        // 배송비 계산
        foreach ($result as $k => &$item) {
          if ($item['cust_code'] === '1029') {
            $item['price'] = round($item['price'] * $ex_list['USD']);
          }

          if ($item['cust_code'] === '1021') {
            $item['price'] = round($item['price'] * $ex_jpy);
          }

          if ($item['cust_code'] === '1029') {
            $item['price'] = $item['price'] * $total_weight;
          }

          if ($item['cust_code'] === '1030') {
            $item['price'] = $item['price'] * $max_weight;
          }

          $oil_percent = max($item['wr_percent'], 0);
          $item['oil_price'] = round($item['price'] * $oil_percent);

          $item['total_price'] = $item['oil_price'] + $item['price'];
        }

        // 가장 저렴한 배송사 선택
        usort($result, function ($a, $b) {
          return $a['total_price'] - $b['total_price'];
        });

        if (!empty($result)) {
          $cheapest = $result[0];
          $delivery_fee = $cheapest['price'];
          $delivery_oil = $cheapest['oil_price'];

          $wr_delivery_fee = $delivery_fee + $delivery_oil;
        }


        if (isEmpty($wr_delivery_fee2)) {
          $insert_sql .= ",wr_delivery_fee2 = '{$wr_delivery_fee}' ";
        } else {
          $insert_sql .= ",wr_delivery_fee = '{$wr_delivery_fee}' ";
        }
      }

      $insert_sql .= ",wr_delivery = '{$delivery_data['wr_code']}' ";

    } else if ($wr_delivery_fee) { # 배송비 O
      $insert_sql .= ",wr_delivery_fee = '{$wr_delivery_fee}' ";
    }

    $sql = "update g5_sales3_list set 
		wr_rack = '{$rack_id}',
		wr_release_use = 1,
		wr_excel_release = 1,
		wr_release_date = '" . G5_TIME_YMDHIS . "',
		wr_release_mbid = '" . $member['mb_id'] . "',
		wr_excel_mbid = '" . $member['mb_id'] . "'
		{$insert_sql}
		where seq = '{$chk['seq']}' limit 1
		";


    if (sql_query($sql, true)) {
      $sql = "UPDATE g5_sales2_list SET ";
      $sql .= ltrim($insert_sql, ',');
      $sql .= "WHERE wr_order_num = '" . $chk['wr_order_num'] . "' limit 1";


      sql_query($sql);

      $sql = "UPDATE g5_sales1_list SET ";
      $sql .= ltrim($insert_sql, ',');
      $sql .= "WHERE wr_order_num = '" . $chk['wr_order_num'] . "' limit 1";
      sql_query($sql);

      $sql = "UPDATE g5_sales0_list SET ";
      $sql .= ltrim($insert_sql, ',');
      $sql .= "WHERE wr_order_num = '" . $chk['wr_order_num'] . "' limit 1";
      sql_query($sql);


      $succ_count++;
    } else {
      $fail_count++;
    }


  }
}

$g5['title'] = '엑셀출고등록등록 결과';
include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);
?>

  <div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
      <p>엑셀 출고등록을 완료했습니다.<br>출고등록 페이지에서 새로고침해주세요.</p>
    </div>

    <dl id="chkfile_result">
      <dt>총 건수</dt>
      <dd><?php echo number_format($total_count); ?></dd>
      <dt>완료건수</dt>
      <dd><?php echo number_format($succ_count); ?></dd>
      <dt>실패건수(재고 랙 없음)</dt>
      <dd><?php echo number_format($fail_count2); ?></dd>
      <dt>실패건수(데이터 없음)</dt>
      <dd><?php echo number_format($fail_count); ?></dd>
    </dl>

    <div class="btn_win01 btn_win">
      <button type="button" onclick="window.close();">창닫기</button>
    </div>

  </div>

<?php
include_once(G5_PATH . '/tail.sub.php');