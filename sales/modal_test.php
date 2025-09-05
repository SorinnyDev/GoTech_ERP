<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

$seq = "75457";

if (!$seq) alert_close('잘못된 접근입니다.');


$sales3 = sql_fetch("SELECT * FROM g5_sales3_list WHERE seq = '$seq'");

$sql = "select * from g5_sales3_list where wr_order_num LIKE '%{$sales3['wr_ori_order_num']}%' AND wr_warehouse='" . $sales3['wr_warehouse'] . "' order by wr_order_num asc";
$hap = sql_query($sql);

$total_weight = 0;
$volume = 0;
while ($item = sql_fetch_array($hap)) {
  $wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$item['wr_product_id']}'");

  $total_weight += (float)$wr_weight3['wr_10'] * (int)$item['wr_ea'];
  $volume += ((float)$wr_weight3['wr_weight3'] * (int)$item['wr_ea']);
}

$total_weight = $sch_total_weight ?? $total_weight;
$volume = $sch_volume ?? $volume;

$max_weight = max($total_weight, $volume);

$sales3['total_weight'] = $total_weight;
$sales3['volume'] = $volume;

$delivery = [];

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$sales3['wr_country']}'");
$country = $country_dcode['code'];

$sql = "SELECT {$country} as price, cust_code, weight_code, B.code_percent, C.wr_name FROM g5_shipping_price A
		LEFT OUTER JOIN g5_code_list B ON B.code_type='3' AND B.code_value=A.cust_code 
		LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
        WHERE weight_code >= {$max_weight} AND {$country} != 0 and C.wr_use='1' and B.code_use='Y' GROUP BY cust_code ORDER BY price ASC";
$result = sql_fetch_all($sql);


foreach ($result as $k => &$item) {
  $oil_percent = max((float)$item['code_percent'] - 1, 0);

  $item['oil_price'] = round($item['price'] * $oil_percent);
}

?>
  <style>
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
        <input type="text" name="sch_total_weight" value="<?= $sch_total_weight ?? $sales3['total_weight'] ?>" class="frm_input required" required placeholder="총 무게">
        <input type="text" name="sch_volume" value="<?= $sch_volume ?? $sales3['volume'] ?>" class="frm_input required" required placeholder="총 부피">
        <input type="text" name="weight_type" value="kg" class="frm_input required" required placeholder="무게 단위" disabled>
        <button class="btn_b01">검색</button>
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
                <td><input type="radio" value="<?= $row['cust_code'] ?>" id="<?= $row['cust_code'] ?>" name="cust_code"></td>
                <td><?php echo $row['cust_code'] ?></td>
                <td><?php echo $row['wr_name'] ?></td>
                <td><?php echo $row['weight_code'] ?></td>
                <td><input type="text" class="wr_name frm_input tw-w-[100px]" name="wr_delivery_fee" value="<?= $row['price'] ?>"></td>
                <td><input type="text" class="wr_percent frm_input" name="wr_delivery_fee2" value="" style="text-align:right"></td>
                <td><input type="text" class="wr_percent frm_input" name="wr_delivery_oil" value="<?= $row['oil_price'] ?>" style="text-align:right"></td>
                <td><input type="text" class="wr_percent frm_input" value="<?= $row['oil_price'] + $row['price'] ?>" style="text-align:right" disabled></td>
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

      if (selectedRadio.length === 0) {
        alert("배송사를 선택하세요.");
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
        wr_delivery_oil: wr_delivery_oil
      };

      // AJAX 요청
      $.post("./ajax.delivery_weight_save.php", formData, function (data) {
        if (data.status === 'Y') {
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
    })

    function checkedArea(cust_code) {
      document.getElementById(cust_code).checked = true;
    }

  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');