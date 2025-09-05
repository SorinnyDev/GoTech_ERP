<?php
include_once('./_common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_19_s)
  $wr_19_s = G5_TIME_YMD;

if (!$wr_19_e)
  $wr_19_e = G5_TIME_YMD;
?>
  <link rel="stylesheet" href="<?php echo G5_ADMIN_URL ?>/css/admin.css">
  <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
      .not_item td {
          background: red;
          color: #fff
      }
  </style>
  <style>
      .cnt_left {
          padding: 5px 10px;
          border-right: 1px solid #ddd;
          word-break: text-overflow: ellipsis;
          overflow: hidden;
          white-space: nowrap;
      }

      .list_03 li {
          padding: 0
      }

      .modify {
          cursor: pointer
      }

      .tbl_frm01 th {
          background: #6f809a;
          color: #fff;
          border: 1px solid #60718b;
          font-weight: normal;
          text-align: center;
          padding: 8px 5px;
          font-size: 0.92em;
          width: 100px
      }

      .tbl_frm01 td {
          border-bottom: 1px solid #ddd;
      }

      .tbl_frm01 td input {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%
      }

      .tbl_frm01 input.readonly {
          background: #f2f2f2
      }
  </style>
  <form name="addform" id="addform" action="./sales1_etc_form_update.php" method="post">
    <div class="new_win">
      <h1>기타발주등록</h1>

      <div class="tbl_frm01 tbl_wrap" style="margin:0;background:#fff">
        <input type="hidden" name="seq" value="<?php echo $seq ?>">
        <table>
          <tr style="display:none">
            <th>도메인명</th>
            <td><input type="text" name="wr_domain" value="기타발주" class="readonly" readonly></td>
            <th>매출일자</th>
            <td><input type="date" name="wr_date" value="<?php echo G5_TIME_YMD ?>" class="readonly" readonly></td>
            <th>주문번호</th>
            <td><input type="text" name="wr_order_num" value="<?php echo $row['wr_order_num'] ?>" class="readonly" readonly></td>
            <th>주문자ID</th>
            <td><input type="text" name="wr_mb_id" value="<?php echo $row['wr_mb_id'] ?>" class="readonly" readonly></td>
            <th>주문자명</th>
            <td><input type="text" name="wr_mb_name" value="<?php echo $row['wr_mb_name'] ?>" class="readonly" readonly></td>
          </tr>
          <tr style="display:none">
            <th>우편번호</th>
            <td><input type="text" name="wr_zip" value="<?php echo $row['wr_zip'] ?>" class="readonly" readonly></td>
            <th>주소</th>
            <td colspan="7" style="text-align:left">
              <input type="text" name="wr_addr1" value="<?php echo $row['wr_addr1'] ?>" style="width:48%" class="readonly" readonly>
              <input type="text" name="wr_addr2" value="<?php echo $row['wr_addr2'] ?>" style="width:48%" class="readonly" readonly>
            </td>
          </tr>

          <tr style="display:none">
            <th>도시명</th>
            <td><input type="text" name="wr_city" value="<?php echo $row['wr_city'] ?>" class="readonly" readonly></td>
            <th>주명</th>
            <td><input type="text" name="wr_ju" value="<?php echo $row['wr_ju'] ?>" class="readonly" readonly></td>
            <th>나라명</th>
            <td><input type="text" name="wr_country" value="<?php echo $row['wr_country'] ?>" class="readonly" readonly></td>
            <th>전화번호</th>
            <td><input type="text" name="wr_tel" value="<?php echo $row['wr_tel'] ?>" class="readonly" readonly></td>
            <th>이메일</th>
            <td><input type="text" name="wr_email" value="<?php echo $row['wr_email'] ?>" class="readonly" readonly></td>
          </tr>

          <tr>
            <th>제품코드</th>
            <td colspan="10">
              <i class="fa fa-search" aria-hidden="true" onclick="add_pop()" style="cursor:pointer"></i>
              <input type="text" name="wr_code" value="<?php echo $row['wr_code'] ?>" style="width:20%" class="readonly" readonly>
              <input type="text" name="wr_product_name1" value="<?php echo $item['wr_2'] ?>" style="width:20%" class="readonly" readonly>
              <input type="text" name="wr_product_name2" value="<?php echo $item['wr_subject'] ?>" style="width:48%" class="readonly" readonly>

            </td>

          </tr>
          <tr style="display:none">
            <th>수량</th>
            <td><input type="text" name="wr_ea" value="<?php echo $row['wr_ea'] ?>" class="readonly" readonly></td>
            <th>박스수</th>
            <td><input type="text" name="wr_box" value="<?php echo $row['wr_box'] ?>" class="readonly" readonly></td>
            <th>단가</th>
            <td><input type="text" name="wr_danga" value="<?php echo $row['wr_danga'] ?>" class="readonly" readonly></td>
            <th>신고가격</th>
            <td><input type="text" name="wr_singo" value="<?php echo $row['wr_singo'] ?>" class="readonly" readonly></td>
            <th>통화</th>
            <td><input type="text" name="wr_currency" value="<?php echo $row['wr_currency'] ?>" class="readonly" readonly></td>
          </tr>
          <tr>
            <th>개당무게</th>
            <td><input type="text" name="wr_weight1" value="<?php echo $row['wr_weight1'] ?>" class="readonly" readonly></td>
            <th>총 무게</th>
            <td><input type="text" name="wr_weight2" value="<?php echo $row['wr_weight2'] ?>" class="readonly" readonly></td>
            <th>무게단위</th>
            <td><input type="text" name="wr_weight_dan" value="<?php echo $item['wr_11'] ?>" class="readonly" readonly></td>
            <th>HS코드</th>
            <td><input type="text" name="wr_hscode" value="<?php echo $row['wr_hscode'] ?>" class="readonly" readonly></td>
            <th>제조국가</th>
            <td><input type="text" name="wr_make_country" value="<?php echo $row['wr_make_country'] ?>" class="readonly" readonly></td>
          </tr>
          <tr style="display:none">
            <th>배송사</th>
            <td><input type="text" name="wr_delivery" value="<?php echo $row['wr_delivery'] ?>" class="readonly" readonly></td>
            <th>배송요금</th>
            <td><input type="text" name="wr_delivery_fee" value="<?php echo $row['wr_delivery_fee'] ?>" class="readonly" readonly></td>
            <th>Service Type</th>
            <td><input type="text" name="wr_servicetype" value="<?php echo $row['wr_servicetype'] ?>" class="readonly" readonly></td>
            <th>packaging</th>
            <td><input type="text" name="wr_packaging" value="<?php echo $row['wr_packaging'] ?>" class="readonly" readonly></td>
            <th>수출국가</th>
            <td><input type="text" name="wr_country_code" value="<?php echo $row['wr_country_code'] ?>" class="readonly" readonly></td>
          </tr>
          <tr>
            <th>수출신고품명</th>
            <td colspan="3"><input type="text" name="wr_name2" value="<?php echo $row['wr_name2'] ?>" class="readonly" readonly></td>
            <th>도메인</th>
            <td colspan="3">
              <select name="wr_domain" required style="height:27px">
                <option value="">선택하세요</option>
                <?php echo get_domain_option('') ?>

              </select>
            </td>
            <th>발주일자</th>
            <td colspan="4"><input type="date" name="wr_date2" value="<?php echo G5_TIME_YMD ?>" class="" required></td>

          </tr>
          <tr>
            <th>발주주문번호</th>
            <td><input type="text" name="wr_order_num2" value="<?php echo $row['wr_order_num2'] ?>"></td>
            <th>발주처</th>
            <td>
              <select name="wr_orderer" class="search_sel" style="width: 100%;" required>

                <?php //echo get_domain_option($wr_18);
                $code_list = get_code_list('5');
                foreach ($code_list as $key => $value) {
                  $selected = ($domain == $value['code_value']) ? "selected" : "";
                  echo "<option value=\"{$value['idx']}\" {$selected}>{$value['code_name']}</option>";
                }

                ?>
              </select>
              <select name="metadata_code_card" class="frm_input search_sel" style="background:#FFFFFF; width: 100%;">
                <option value="0">결제카드</option>
                <?php
                $arr = get_code_list('7');
                foreach ($arr as $key => $v) {
                  $selceted = $v['idx'] == $card['value'] ? "selected" : "";
                  echo "<option value='{$v['idx']}' {$selceted}>{$v['code_name']}</option>";
                }
                ?>
              </select>

            </td>
            <th>발주수량</th>
            <td><input type="text" name="wr_order_ea" class="add_wr_order_ea" value="<?php echo $row['wr_order_ea'] ?>" required></td>
            <th>발주단가</th>
            <td><input type="text" name="wr_order_price" class="add_wr_order_price" value="<?php echo $row['wr_order_price'] ?>" required></td>
            <th>배송비</th>
            <td><input type="text" name="wr_order_fee" class="add_wr_order_fee" value="<?php echo $row['wr_order_fee'] ?>" required></td>

          </tr>
          <tr>
            <th>발주금액</th>
            <td><input type="text" name="wr_order_total" class="add_wr_order_total" value="<?php echo $row['wr_order_total'] ?>"></td>
            <th>트래킹NO</th>
            <td><input type="text" name="wr_order_traking" value="<?php echo $row['wr_order_traking'] ?>"></td>
            <th>비고</th>
            <td colspan="5"><input type="text" name="wr_order_etc" value="<?php echo $row['wr_order_etc'] ?>"></td>

          </tr>
        </table>

      </div>


      <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;">
        <input type="hidden" name="wr_id" value="">
        <input type="submit" value="기타 발주생성" class="btn_submit btn" onclick="document.pressed=this.value">

        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
      </div>
  </form>


  </div>
  <script>
    $(document).ready(function () {
      $('.search_sel').select2();

      $(document).on('keyup', '.add_wr_order_ea, .add_wr_order_price, .add_wr_order_fee', function () {
        let ea = parseInt($('.add_wr_order_ea').val());
        let price = parseInt($('.add_wr_order_price').val());
        let fee = parseInt($('.add_wr_order_fee').val());

        if (isNaN(ea) || typeof ea == 'number' && ea < 0) {
          ea = 0;
        }

        if (!isDefined(price)) {
          price = 0;
          $(".add_wr_order_price").val(0);
        }
        if (!isDefined(fee)) {
          fee = 0;
          $(".add_wr_order_fee").val(0);
        }
        let total = price * ea + fee;


        $('.add_wr_order_total').val(total);

      });

    });

    function add_pop() {

      window.open("./item_pop.php", "item_pop", "left=50, top=50, width=750, height=650, scrollbars=1");

    }

    function item_info(wr_id) {
      $('input[name=wr_id]').val(wr_id);
      $.ajax({
        type: 'POST',
        url: "./ajax.item_info.php",
        data: {wr_id: wr_id},
        dataType: "json",
        success: function (data, status, xhr) {

          $('input[name=wr_weight1]').val(data.wr_10);
          $('input[name=wr_weight_dan]').val(data.wr_11);
          $('input[name=wr_hscode]').val(data.wr_12);
          $('input[name=wr_make_country]').val(data.wr_13);
          $('input[name=wr_name2]').val(data.wr_3);
          $('input[name=wr_order_price]').val(data.wr_22);

        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR.responseText);
        }
      });


    }

    function selectAll(selectAll) {
      const checkboxes
        = document.getElementsByName('chk_seq[]');

      checkboxes.forEach((checkbox) => {
        if (checkbox.disabled == true) {

        } else {
          checkbox.checked = selectAll.checked;
        }
      })
    }

    function chkfrm(f) {
      f.act.value = document.pressed;

      if (document.pressed == "발주생성") {

        if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
          return false;
        }
      }

      if (document.pressed == "한국창고 출고") {

        if (!confirm("선택한 매출자료를 한국창고에서 출고등록 하시겠습니까?")) {
          return false;
        }
      }

      if (document.pressed == "미국창고 출고") {

        if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
          return false;
        }
      }
    }
  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');