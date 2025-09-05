<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$seq) alert_close('잘못된 접근입니다.');

$sales3 = sql_fetch("SELECT * FROM g5_sales3_list WHERE seq = '$seq'");

$sql = "select * from g5_sales3_list where wr_ori_order_num = '{$sales3['wr_ori_order_num']}' AND wr_warehouse='" . $sales3['wr_warehouse'] . "' order by wr_order_num asc";
$hap = sql_query($sql);

$total_weight = 0;
$total_weight2 = 0;
while ($item = sql_fetch_array($hap)) {
  $wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$item['wr_product_id']}'");

  $total_weight += (float)$wr_weight3['wr_10'] * (int)$item['wr_ea'];
  $total_weight2 += (float)$wr_weight3['wr_weight3'] * (int)$item['wr_ea'];
}

$total_weight = $weight ?? $total_weight;

$weight1 = $sales3['wr_weight1'] ?? 0; # 중량 무게1
$weight2 = isNotEmpty($sales3['wr_weight2']) ? $sales3['wr_weight2'] : 0; # 중량 무게2
$weight3 = isNotEmpty($sales3['wr_weight3']) ? $sales3['wr_weight3'] : $total_weight2; # 부피

$max_weight = max($total_weight, $weight1, $weight2, $weight3);

$sales3['total_weight'] = $total_weight;

# 환율 정보
$sql = "select rate, ex_eng from g5_excharge";
$result = sql_fetch_all($sql);

$ex_list = array_column($result, 'rate', 'ex_eng');

$ex_jpy = $ex_list['JPY'] / 100;

$delivery = [];

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$sales3['wr_country']}'");
$country = $country_dcode['code'];

if ($sales3['wr_servicetype'] === '0003') {
  $sql = "
    SELECT N1234 as price, cust_code, weight_code, C.wr_percent, C.wr_name
    FROM g5_shipping_price A
             LEFT OUTER JOIN g5_delivery_company C ON C.wr_code = A.cust_code
    WHERE {$country} != 0
      and C.wr_use = '1'
      and weight_code = '1'
      and cust_code in ('1002', '1009', '1029', '1030')
    GROUP BY cust_code
    ORDER BY price ASC
  ";
} else {
  $sql = "SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name FROM g5_shipping_price A
		LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
        WHERE weight_code >= {$max_weight} AND {$country} != 0 and C.wr_use='1' GROUP BY cust_code ORDER BY price ASC";
}

$result = sql_fetch_all($sql);


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

usort($result, function ($a, $b) {
  return $a['total_price'] - $b['total_price'];
});

?>
  <style>
      .tbl_frm01 th {
          background: #6f809a;
          color: #fff;
          border: 1px solid #60718b;
          font-weight: normal;
          text-align: center;
          padding: 8px 5px;
          font-size: 0.92em
      }

      .tbl_frm01 td {
          border-bottom: 1px solid #ddd;
          padding: 10px 10px
      }

      .tbl_frm01 td input {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%;
          min-width: 150px;
          height: 30px
      }

      .tbl_frm01 .btn_b02 {
          height: 30px;
          line-height: 30px
      }

      .tbl_frm01 input.readonly {
          background: #f2f2f2
      }
      .not_item td {
          background: red;
          color: #fff
      }

      .pg a {
          margin: 0 5px
      }

      .tbl_head01 td {
          background: none
      }

      .move_stock th {
          width: 100px;
          background: #ddd
      }

      .move_stock select {
          width: 30% !important
      }

      .move_stock button {
          width: 30% !important;
          height: 35px;
          line-height: 35px;
          border: 1px solid #2aba8a;
          background: #2aba8a;
          color: #fff
      }

      .move_stock2 td {
          padding: 15px
      }

      .down_arrow {
          position: relative
      }

      .down_arrow::after {
          content: "↓";
          position: absolute;
          top: -14px;
          font-size: 20px;
          font-weight: 600;
          left: 50%;
          background: #fff;
          margin-left: -20px;
          color: #2aba8a
      }

      .diabled_btn {
          background: #ddd;
          cursor: not-allowed
      }
  </style>
  <div class="new_win">
    <h1>최적 배송사 계산</h1>
    <div id="excelfile_upload">
      <form name="addFrm" method="get" autocomplete="off" style="padding-bottom:10px;">
        <input type="hidden" name="seq" value="<?= $seq ?>">
        <input type="hidden" name="service_type" value="<?= $sales3['wr_servicetype'] ?>">
        <div class="tbl_wrap tbl_frm01">
        <table>
          <tbody>
          <tr>
            <th style="width: 15%;">나라</th>
            <td>
              <select name="country" class="search_sel tw-w-[150px]">
                <?php
                $topSql = "select * from g5_country order by wr_country_en ASC";
                $topRst = sql_query($topSql);
                for ($i = 0; $top = sql_fetch_array($topRst); $i++) {

                  ?>
                  <option value="<?php echo $top['wr_code'] ?>" <?php echo get_selected($top['wr_code'], $country) ?>><?php echo $top['wr_country_ko'] ?>(<?php echo $top['wr_code'] ?>)</option>
                <?php } ?>
              </select>
            </td>
            <th style="width: 15%;">부피 무게</th>
            <td><input type="text" name="weight3" class="frm_input" style="width:20%" value="<?php echo urldecode($weight3 ?? $total_weight) ?>"> kg
            </td>
          </tr>
          <tr>
            <th style="width: 15%;">무게</th>
            <td><input type="text" name="weight" class="frm_input" style="width:20%" value="<?php echo urldecode($weight ?? $total_weight) ?>"> kg
            </td>
            <th style="width: 15%;">가로(W)</th>
            <td><input type="text" name="width" id="width" class="frm_input" style="width:20%" value="<?php echo urldecode($width) ?>"> mm
            </td>
          </tr>
          <tr>
            <th>길이(L)</th>
            <td><input type="text" name="length" id="length" class="frm_input" style="width:20%" value="<?php echo urldecode($length) ?>"> mm
            </td>
            <th>높이(H)</th>
            <td><input type="text" name="height" id="height" class="frm_input" style="width:20%" value="<?php echo urldecode($height) ?>"> mm
            </td>
          </tr>
          <tr>
            <th>중량무게1</th>
            <td><input type="text" name="weight1" id="weight1" class="frm_input" style="width:20%" value="<?php echo urldecode($weight1) ?>"> kg
            </td>
            <th>중량무게2</th>
            <td><input type="text" name="weight2" id="weight2" class="frm_input" style="width:20%" value="<?php echo urldecode($weight2) ?>"> kg
            </td>
          </tr>

          </tbody>
        </table>
        </div>
        <div style="width:100%;text-align:center;margin-top:20px">
          <button class="btn_b01">배송비 검색</button>
        </div>
      </form>
    </div>

    <form name="frm" method="post">
      <input type="hidden" name="seq" value="<?=$sales3['seq']?>">
      <div id="excelfile_upload" class="result_list" style="padding:12px;">

        <div style="clear:both"></div>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">

            <tr>
              <th></th>
              <th>코드</th>
              <th>배송사명</th>
              <th>무게</th>
              <th>배송요금</th>
              <th>추가 배송비</th>
              <th>유류할증료</th>
              <th>배송 총금액</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $k => $row) {
              $bg = 'bg' . ($k % 2);
              ?>

              <tr class="<?php echo $bg; ?>" onclick="checkedArea('<?= $row['cust_code'] ?>');">
                <td><input type="radio" value="<?= $row['cust_code'] ?>" id="<?= $row['cust_code'] ?>" name="cust_code" <?=$k == 0 ? 'checked' : ''?>></td>
                <td><?php echo $row['cust_code'] ?></td>
                <td><?php echo $row['wr_name'] ?></td>
                <td><?php echo $row['weight_code'] ?></td>
                <td><input type="text" class="wr_name frm_input tw-w-[100px]" name="wr_delivery_fee" value="<?= round($row['price']) ?>"></td>
                <td><input type="text" class="wr_percent frm_input" name="wr_delivery_fee2" value="" style="text-align:right"></td>
                <td><input type="text" class="wr_percent frm_input" name="wr_delivery_oil" value="<?= $row['oil_price'] ?>" style="text-align:right"></td>
                <td><input type="text" class="wr_percent frm_input" value="<?= round($row['total_price']) ?>" style="text-align:right" disabled></td>
              </tr>
            <?php } ?>
            </tbody>


          </table>
        </div>
        <div class="tw-flex tw-justify-center tw-space-x-5">
          <button type="button" class="btn_b01" onclick="fnSubmit();">적용</button>
          <button type="button" class="btn_b02" onclick="window.close();">닫기</button>
        </div>
      </div>


  </div>


  </div>
  <script>

    function fnSubmit() {
      // 선택된 라디오 버튼 찾기
      const selectedRadio = $("input[name='cust_code']:checked");
      const serviceType = $("input[name='service_type']").val();

      if (selectedRadio.length === 0) {
        alert("배송사를 선택하세요.");
        return;
      }

      if (!confirm("적용 및 저장 하시겠습니까?")) {
        return;
      }

      const custCode = selectedRadio.val(); // 선택된 배송사 코드
      const $row = selectedRadio.closest("tr"); // 해당 행 찾기

      // 해당 행에서 데이터 수집
      const wr_delivery_fee = $row.find("input[name='wr_delivery_fee']").val();
      const wr_delivery_fee2 = $row.find("input[name='wr_delivery_fee2']").val();
      const wr_delivery_oil = $row.find("input[name='wr_delivery_oil']").val();

      // 데이터를 객체로 구성
      const formData = {
        seq: $("input[name='seq']").val(),
        cust_code: custCode,
        wr_delivery_fee: wr_delivery_fee,
        wr_delivery_fee2: wr_delivery_fee2,
        wr_delivery_oil: wr_delivery_oil,
        service_type: serviceType
      };

      // AJAX 요청
      $.post("./ajax.delivery_weight_save.php", formData, function (data) {
        if (data.status === 'Y') {
          window.opener?.postMessage({event: 'popup_submit', seq: $("input[name='seq']").val()}, '*');
          window.close();
        } else {
          alert("서버 오류입니다.");
        }
      }, 'json');
    }

    $(function () {
      $(".tbl_wrap").on("input", "input", function () {
        var $row = $(this).closest("tr");

        // 배송요금, 추가 배송비, 유류할증료 값 가져오기
        var deliveryFee = parseFloat($row.find("input:eq(1)").val()) || 0;
        var additionalFee = parseFloat($row.find("input:eq(2)").val()) || 0;
        var fuelSurcharge = parseFloat($row.find("input:eq(3)").val()) || 0;

        // 배송 총금액 계산
        var totalAmount = deliveryFee + additionalFee + fuelSurcharge;

        // 배송 총금액 필드 업데이트
        $row.find("input:eq(4)").val(totalAmount);
      });

      $('#width, #height, #length').bind('keyup', function () {

        let width = parseInt($('#width').val());
        let height = parseInt($('#height').val());
        let length = parseInt($('#length').val());

        if (isNaN(width)) {
          width = 0;
        }
        if (isNaN(height)) {
          height = 0;
        }
        if (isNaN(length)) {
          length = 0;
        }

        weight_calculation(1, width, height, length);
        weight_calculation(2, width, height, length);

      })
    })

    function checkedArea(cust_code) {
      document.getElementById(cust_code).checked = true;
    }

    function weight_calculation(type, width, height, length) {
      let calc = parseInt(width) * parseInt(height) * parseInt(length);
      let total = 0;
      if (type == 1) {
        //중량무게1 가로*세로*높이/5000
        total = calc / 5000000;
        $('#weight1').val(total.toFixed(2));
      } else if (type == 2) {
        //중량무게2 가로*세로*높이/6000
        total = calc / 6000000;
        $('#weight2').val(total.toFixed(2));
      }

    }

    function refreshWithSeq() {
      const url = new URL(window.location.href);
      const params = new URLSearchParams(url.search);

      if (!params.has('seq')) {
        params.set('seq', '<?=$seq?>');
      }

      window.location.href = `${url.pathname}?${params.toString()}`;
    }



  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');