<?php
include_once('./_common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);


$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");

switch ($warehouse) {
  // 한국
  case "1000" :
    $name = "한국창고";
    $store_stock = $it['wr_32'];
    $sql_add = "wr_32";
    break;

  // 미국
  case "3000" :
    $name = "미국창고";
    $store_stock = $it['wr_36'];
    $sql_add = "wr_36";
    break;

  // FBA
  case "4000" :
    $name = "FBA창고";
    $store_stock = $it['wr_42'];
    $sql_add = "wr_42";
    break;

  // W-FBA
  case "5000" :
    $name = "W-FBA창고";
    $store_stock = $it['wr_43'];
    $sql_add = "wr_43";
    break;

  // U-FBA
  case "6000" :
    $name = "U-FBA창고";
    $store_stock = $it['wr_44'];
    $sql_add = "wr_44";
    break;

  case "7000" :
    $name = "한국반품창고";
    $store_stock = $it['wr_40'];
    $sql_add = "wr_40";
    break;

  case "8000" :
    $name = "미국반품창고";
    $store_stock = $it['wr_41'];
    $sql_add = "wr_41";
    break;

  case "11000" :
    $name = "한국폐기창고";
    $store_stock = $it['wr_45'];
    $sql_add = "wr_45";
    break;
  case "12000" :
    $name = "미국폐기창고";
    $store_stock = $it['wr_46'];
    $sql_add = "wr_46";
    break;


  default :

}
?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    //문자열검색시 대소문자 구분없이 되도록
    $.expr[':'].icontains = function (a, i, m) {
      return $(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
    };
  </script>
  <style>
      .select2-container--default .select2-selection--single {
          height: 40px;
          border: 1px solid #d9dee9;
          background: #f1f3f6
      }

      .select2-container--default .select2-selection--single .select2-selection__rendered {
          line-height: 38px
      }

      .select2-container--default .select2-selection--single .select2-selection__arrow b {
          margin-top: 4px
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

      @media (max-width: 767px) {
          .result_list {
              width: 100% !important
          }
      }

      .total_qty_input {
          width: 50px;
          border: 0;
          text-align: right;
          padding: 1px 3px;
          background: #505b6a;
          color: #fff;
          border: 1px solid #353a40
      }

      .total_qty_btn {
          border: 0;
          padding: 1px 3px;
          background: #505b6a;
          color: #fff;
          border: 1px solid #353a40
      }
  </style>
  <div class="new_win">
    <h1>재고 이동로그</h1>

    <form name="searchFrm" method="get" autocomplete="off">
      <div id="excelfile_upload">
        <strong>창고 위치</strong> <?php echo $name ?><br>
        <strong>SKU</strong> <?php echo $it['wr_1'] ?><br>
        <strong>제품명</strong> <?php echo $it['wr_subject'] ?><br><br>

        <?php if ($it['wr_rack']) { ?>
          <strong>담당자 지정랙</strong> [<?php echo PLATFORM_TYPE[$it['wr_warehouse']] ?>] <?php echo get_rack_name($it['wr_rack']) ?>
        <?php } ?>
      </div>
    </form>


    <div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">
      <div class="tbl_head01 tbl_wrap" style="width:100%">
        <table>
          <thead>
          <tr>
            <th>일자</th>
            <th>랙 번호</th>
            <th>수량</th>
            <th>사유</th>
            <th>담당자</th>
            <!--<th>관리</th>-->
          </tr>
          </thead>
          <tbody>
          <?php
          $sql = "select a.*, b.gc_name from g5_rack_stock a LEFT JOIN g5_rack b ON(a.wr_rack = b.seq) where a.wr_product_id = '{$wr_id}' and a.wr_warehouse = '{$warehouse}'";
          $rst = sql_query($sql);
          for ($i = 0; $row = sql_fetch_array($rst); $i++) {
            $mb = get_member($row['wr_mb_id'], 'mb_name');
            if ($row['wr_sales3_id']) {
              if ($row['wr_move_log'] == "") {
                $log = "출고등록";
              } else {
                $log = $row['wr_move_log'];
              }
            } else {
              $log = $row['wr_move_log'];
            }
            ?>
            <tr>
              <td><?php echo date('y-m-d H:i:s', strtotime($row['wr_datetime'])) ?></td>
              <td><?php echo $row['gc_name'] ?></td>
              <td><?php echo $row['wr_stock'] ?></td>
              <td><?php echo $log ?></td>
              <td><?php echo $mb['mb_name'] ?></td>
              <!--<td class="td_mng td_mng_m"><button type="button" class="btn btn_03" onclick="del_confirm(<?php echo $row['seq'] ?>)">삭제</button></td>-->
            </tr>
            <?php
            $total += (int)$row['wr_stock'];
          }
          if ($total != (int)$store_stock) {
            @sql_query("update g5_write_product set {$sql_add} = '{$total}' where wr_id = '{$wr_id}'");
            echo '<script>alert(\'랙 재고와 총 재고 수량이 맞지 않아 자동조정 되었습니다.\\n랙 재고: ' . (int)$total . '\\n' . $name . ' 총 재고 : ' . (int)$store_stock . '\'); location.reload()</script>';
          }

          ?>
          </tbody>
          <tfoot>
          <tr>
            <th colspan="2">총 합계</th>
            <td><?php echo number_format($total) ?></td>
            <td colspan="2"></td>

          </tr>
          <tr>
            <th colspan="2"><?php echo $name ?> 재고</th>
            <td><?php echo number_format($store_stock) ?></td>
            <td colspan="2"></td>

          </tr>
          </tfoot>
        </table>
      </div>
      <div style="clear:both"></div>


    </div>

    <div style="clear:both;margin-bottom:100px"></div>
    <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0;">

      <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
  </div>

  <script>
    function del_confirm(seq) {
      if (confirm('재고 기록을 삭제하시겠습니까? 삭제하는 수량만큼 재고가 변경됩니다.')) {
        location.href = './rack_stock_log_update.php?w=d&seq=' + seq;

      } else {
        return false;
      }
    }

    $(function () {

      $('.ms_rack, .ms_rack2').select2();

      //모바일 편의성 대응
      $('.qty').bind('focus', function () {
        let ea = $(this).val();

        if (ea == 0) {
          $(this).val('');
        }
      })

      $('.qty').bind('blur', function () {
        let ea = $(this).val();

        if (ea == '') {
          $(this).val('0');
        }
      })

      $('.qty').bind('keyup', function () {

        let ea = $(this).val();
        let ori = $(this).prev().val();

        if (ea == ori) {
          alert('재고 변동이 없습니다.');
          return false;
        }

        if (ea < 0) {
          alert('0이하는 입력하실 수 없습니다.');
          $(this).val(ori);
          return false;
        }
      })

      $('.ms_stock').bind('keyup', function () {

        let rack_ea = parseInt($('.ms_rack option:selected').attr('data'));
        let ea = parseInt($(this).val());

        if (!rack_ea) {
          alert('재고가 있는 랙을 먼저 선택하세요.');
          $(this).val('');
          $('.ms_rack').focus();
          return false;
        }

        if (ea <= 0) {
          alert('0이하는 입력하실 수 없습니다.');
          $(this).val('');
          return false;
        }

        if (ea > rack_ea) {
          alert(rack_ea + '선택 한 랙의 재고보다 많이 입력하실 수 없습니다.');
          $(this).val(rack_ea);
          return false;
        }

      })

      $('.ms_warehouse').bind('change', function () {
        $.post('./ajax.rack.php', {wr_id: <?php echo $wr_id?>, warehouse: $(this).val(), mode: 'a'}, function (data) {
          $('.ms_rack').html(data);
        })
      })

      $('.ms_warehouse2').bind('change', function () {
        $.post('./ajax.rack.php', {wr_id: <?php echo $wr_id?>, warehouse: $(this).val(), mode: ''}, function (data) {
          $('.ms_rack2').html(data);
        })
      })

      $('.del_btn').bind('click', function () {
        if (confirm('정말 해당 매출자료를 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
          let el = $(this).closest('tr');

          $.post('./sales1_delete.php', {seq: $(this).attr('data')}, function (data) {

            if (data == "y") {
              el.remove();
              alert('매출정보가 삭제되었습니다.');
            } else {
              alert('처리 중 오류가 발생했습니다.');
            }
            $('#result_form').html(data);
          })

        } else {
          return false;
        }
      })
    })

    function add_pop(sku, pname, wr_id) {

      window.open("/bbs/write.php?bo_table=product&sku=" + sku + "&pname=" + pname + "&swr_id=" + wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");

    }

    function add_item(wr_code, wr_2, wr_subject) {

      opener.window.document.addform.wr_code.value = wr_code;
      opener.window.document.addform.wr_product_name1.value = wr_2;
      opener.window.document.addform.wr_product_name2.value = wr_subject;

      window.close();
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