<?php
include_once('./_common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

$rack_data = sql_fetch_all("SELECT * FROM g5_rack WHERE gc_use = 1");
$warehouse_group_array = array();

foreach ($rack_data as $item) {
  $warehouse_key = $item['gc_warehouse'];
  if (!isset($warehouse_group_array[$warehouse_key])) {
    $warehouse_group_array[$warehouse_key] = array();
  }
  $warehouse_group_array[$warehouse_key][] = $item;
}

$json_rack_data = json_encode($warehouse_group_array, JSON_UNESCAPED_UNICODE);

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");

$good_product = [];
$refur_product = [];
$dispose_product = [];

foreach ($warehouseConfig as $warehouse_id => $product_details) {
  switch ($product_details['quality']) {
    case 'good':
      $good_product[] = (int)$warehouse_id;
      break;
    case 'refur':
      $refur_product[] = (int)$warehouse_id;
      break;
    case 'dispose':
      $dispose_product[] = (int)$warehouse_id;
      break;
  }
}

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $.expr[':'].icontains = function(a, i, m) {
    return $(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
  };
</script>
<style>
  .rack_selection {
    background: url(../img/wrest.gif) top right no-repeat;
  }

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

  .spinner-icon {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    vertical-align: middle;
    margin-right: 6px;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>
<div class="new_win">
  <h1>재고관리</h1>

  <form name="searchFrm" method="get" autocomplete="off">
    <div id="excelfile_upload">
      <strong>SKU</strong> <?php echo $it['wr_1'] ?><br>
      <strong>제품명</strong> <?php echo $it['wr_subject'] ?><br><br>

      <?php if ($it['wr_rack']) { ?>
        <strong>담당자 지정랙</strong> [<?php echo PLATFORM_TYPE[$it['wr_warehouse']] ?>] <?php echo get_rack_name($it['wr_rack']) ?>
      <?php } ?>
    </div>
  </form>

  <div id="excelfile_upload" class="result_list" style="display:none;padding:12px;float:left;width:47%">
    <h2 style="margin-left:0">기초재고 랙 이동</h2>

    <div class="tbl_frm01 tbl_wrap move_stock ">
      <table>
        <tbody>
          <tr>
            <th>한국창고</th>
            <td>
              <form name="move2Frm" action="./rack_move_update.php" method="post">
                <input type="hidden" name="mode" value="move1_kor">
                <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">

                <select name="ms1_rack" class="required" required>
                  <?php
                  $kor_stock2 = $it['wr_32'] - $kor_stock;
                  $sql_common = " from g5_rack ";
                  $sql_search = " where gc_warehouse = 1000 and gc_use = 1 order by gc_name asc";
                  $sql = " select * {$sql_common} {$sql_search}  ";
                  $result = sql_query($sql);
                  for ($i = 0; $row = sql_fetch_array($result); $i++) {
                  ?>
                    <option value="<?php echo $row['seq'] ?>"><?php echo $row['gc_name'] ?></option>
                  <?php } ?>
                </select>

                <select name="ms1_stock" class="required" required>
                  <?php for ($i = 1; $i <= $kor_stock2; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                  }
                  if ($kor_stock2 == 0) echo '<option value="">없음</option>';
                  ?>
                </select>
                <button>재고이동</button>
              </form>
            </td>
          </tr>

          <tr>
            <th>미국창고</th>
            <td>
              <form name="move2Frm" action="./rack_move_update.php" method="post">
                <input type="hidden" name="mode" value="move1_usa">
                <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">

                <select name="ms1_rack" class="required" required>
                  <?php
                  $usa_stock2 = $it['wr_36'] - $usa_stock;
                  $sql_common = " from g5_rack ";
                  $sql_search = " where gc_warehouse = 3000 and gc_use = 1 order by gc_name asc";
                  $sql = " select * {$sql_common} {$sql_search}  ";
                  $result = sql_query($sql);
                  for ($i = 0; $row = sql_fetch_array($result); $i++) {
                  ?>
                    <option value="<?= $row['seq'] ?>"><?= $row['gc_name'] ?></option>
                  <?php } ?>
                </select>

                <select name="ms1_stock" class="required" required>
                  <?php for ($i = 1; $i <= $usa_stock2; $i++) {

                    echo '<option value="' . $i . '">' . $i . '</option>';
                  }
                  if ($usa_stock2 == 0) echo '<option value="">없음</option>';
                  ?>
                </select>

                <button>재고이동</button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="excelfile_upload" class="result_list" style="padding:12px;float:left;width:100%">
    <h2 style="margin-left:0">일반 재고 랙 이동</h2>
    <div class="tbl_frm01 tbl_wrap move_stock move_stock2">
      <form name="move2Frm" id="move2Frm" action="./rack_move_update.php" method="post">
        <input type="hidden" name="mode" value="move2">
        <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">

        <table>
          <tbody>
            <tr>
              <td>
                <select name="ms_warehouse" class="ms_warehouse">
                  <? foreach (PLATFORM_TYPE as $key => $value) {
                    if ((int)$key > 10000) {
                      continue;
                    }
                  ?>
                    <option value="<?= $key ?>"><?= $value ?>(<?= $key ?>)</option>
                  <? } ?>
                </select>

                <select name="ms_rack" class="ms_rack required" required>
                  <?php
                  $sql = "SELECT
                            rack.seq,
                            rack.gc_name,
                            SUM(rs.wr_stock) AS total
                          FROM
                            g5_rack_stock rs
                          LEFT JOIN
                            g5_rack rack ON rs.wr_rack = rack.seq
                          WHERE
                            rs.wr_warehouse = '1000' AND rs.wr_product_id = '{$wr_id}'
                          GROUP BY
                            rack.gc_name
                          HAVING
                            total > 0
                          ORDER BY
                            total ASC;";

                  $result = sql_query($sql);

                  $all_total = 0;
                  for ($a = 0; $stock = sql_fetch_array($result); $a++) {
                    $all_total += $stock['total'];
                  ?>
                    <option value="<?= $stock['seq'] ?>" data="<?= $stock['total'] ?>">
                      <?= $stock['gc_name'] ?>(재고:<?= $stock['total'] ?>)</option>
                  <?php
                  }
                  if ($all_total == 0) echo '<option value="">재고없음</option>';
                  ?>
                </select>

                <input type="text" name="ms_stock" class="ms_stock frm_input required" placeholder="이동수량" required>
              </td>
            </tr>

            <tr>
              <td class="down_arrow">
                <select name="ms_warehouse2" class="ms_warehouse2 required" required>
                  <option value="">이동창고 선택</option>
                  <? foreach (PLATFORM_TYPE as $key => $value) {
                    if ((int)$key > 10000) {
                      continue;
                    }
                  ?>
                    <option value="<?= $key ?>"><?= $value ?>(<?= $key ?>)</option>
                  <? } ?>
                </select>
                <input type='hidden' name='ms_rack2' class='ms_rack2' value=''>
                <input type='text' name='rack_input' class='rack_input frm_input rack_selection' style='width:326px;'
                  value='' placeholder='창고를 선택하세요' required>

                <button type="submit">재고이동</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>

  <div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">
    <div class="tbl_head02 tbl_wrap" style="width:500px; display: flex;">
      <table>
        <tr>
          <th style="width:100px">임시창고</th>
          <td style="text-align:right"><?php echo number_format($it['wr_37']) ?></td>
          <th style="width:100px">전체재고</th>
          <td style="text-align:right"><?php echo number_format($it['wr_32'] + $it['wr_36'] + $it['wr_37'] + $it['wr_42'] + $it['wr_43'] + $it['wr_44']) ?></td>
        </tr>
      </table>
      <div id="spinner1" style="display: none; text-align: center; padding: 10px; width: 250px;">
        불러오는 중입니다. <span class="spinner-icon"></span>
      </div>
    </div>
    <div id="rack_container_01">
    </div>
  </div>


  <div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">
    <div class="tbl_head02 tbl_wrap" style="width:400px; display: flex;">
      <table>
        <tr>
          <th style="width:100px">반품창고 재고</th>
          <td style="text-align:right"><?php echo number_format($it['wr_40'] + $it['wr_41']) ?></td>
        </tr>
      </table>
      <div id="spinner2" style="display: none; text-align: center; padding: 10px; width: 250px;">
        불러오는 중입니다. <span class="spinner-icon"></span>
      </div>
    </div>

    <div id="rack_container_02">

    </div>
  </div>

  <div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">

    <div class="tbl_head02 tbl_wrap" style="width:400px; display: flex;">
      <table>
        <tr>
          <th style="width:100px">폐기창고 재고</th>
          <td style="text-align:right"><?php echo number_format($it['wr_45'] + $it['wr_46']) ?></td>
        </tr>
      </table>
      <div id="spinner3" style="display: none; text-align: center; padding: 10px; width: 250px;">
        불러오는 중입니다. <span class="spinner-icon"></span>
      </div>
    </div>

    <div id="rack_container_03">

    </div>
  </div>


</div>

<div style="clear:both;margin-bottom:100px"></div>
<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0;">
  <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
</div>
</div>

<script>
  const wr_id = '<?= $wr_id ?>';

  function error_msg() {
    alert('미분류 재고가 존재하여 랙별 이동이 불가합니다.\n미분류 재고 부터 이동 후 실행하세요.');
    return false;
  }

  $(function() {
    rack_data_array = <?= $json_rack_data ?>;

    fetch_pop_rack_container();

    // $('.ms_rack, .ms_rack2').select2();
    $('.ms_rack').select2();

    //모바일 편의성 대응
    $('.qty').bind('focus', function() {
      let ea = $(this).val();

      if (ea == 0) {
        $(this).val('');
      }
    })

    $('.qty').bind('blur', function() {
      let ea = $(this).val();

      if (ea == '') {
        $(this).val('0');
      }
    })

    $('.qty').bind('keyup', function() {

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

    $('.ms_stock').bind('keyup', function() {

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

    $('.ms_warehouse').bind('change', function() {
      $.post('./ajax.rack.php', {
        wr_id: <?php echo $wr_id ?>,
        warehouse: $(this).val(),
        mode: 'a'
      }, function(data) {
        $('.ms_rack').html(data);
      })
    })

    $('.rack_input').on('focus', function() {
      $(this).select(); // 현재 input 필드의 모든 텍스트를 선택합니다.
    });

    $('.rack_input').on('click', function() {
      $(this).select();
    });

    $('.rack_input').on('keydown', function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        var inputValue = $(this).val().trim();
        var targetWarehouse = $('.ms_warehouse2').val();
        let index = -1;

        if (targetWarehouse) {
          const warehouseItems = rack_data_array[targetWarehouse];
          const index = warehouseItems.findIndex(item => item.gc_name === inputValue);

          if (index !== -1) {
            $(this).attr('placeholder', `${index}`);
            $(this).css('background-color', '#93d6ecff');
            $(this).prev('input:hidden').val(warehouseItems[index].seq);
          } else {
            $(this).css('background-color', '');
            $(this).attr('placeholder', '랙 없음');
            $(this).val('');
            $(this).prev('input:hidden').val('');
          }
        } else {
          $(this).val('');
          $(this).attr('placeholder', '창고를 선택하세요');
        }
        $(this).blur(); // input 필드에서 포커스 제거
      }
    });

    $('.del_btn').bind('click', function() {
      if (confirm('정말 해당 매출자료를 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
        let el = $(this).closest('tr');

        $.post('./sales1_delete.php', {
          seq: $(this).attr('data')
        }, function(data) {
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

  function open_log(warehouse, wr_id) {

    window.open("./rack_stock_log.php?warehouse=" + warehouse + "&wr_id=" + wr_id, "log_popup" + wr_id, "left=50, top=50, width=650, height=750, scrollbars=1");

  }

  function add_item(wr_code, wr_2, wr_subject) {

    opener.window.document.addform.wr_code.value = wr_code;
    opener.window.document.addform.wr_product_name1.value = wr_2;
    opener.window.document.addform.wr_product_name2.value = wr_subject;

    window.close();
  }

  function selectAll(selectAll) {
    const checkboxes = document.getElementsByName('chk_seq[]');

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

  async function fetch_pop_rack_container() {
    $("#spinner1, #spinner2, #spinner3").show();
    const group1Keys = <?= json_encode($good_product); ?>;
    const group1Promises = group1Keys.map(key =>
      $.ajax({
        url: './ajax.pop_rack_content.php',
        method: 'POST',
        data: {
          wr_id,
          warehouse_key: key
        }
      })
    );
    const group1Results = await Promise.all(group1Promises);
    group1Results.forEach(html => $("#rack_container_01").append(html));
    $("#spinner1").hide();

    const group2Keys = <?= json_encode($refur_product); ?>;
    const group2Results = await Promise.all(group2Keys.map(key =>
      $.ajax({
        url: './ajax.pop_rack_content.php',
        method: 'POST',
        data: {
          wr_id,
          warehouse_key: key
        }
      })
    ));
    group2Results.forEach(html => $("#rack_container_02").append(html));
    $("#spinner2").hide();

    const group3Keys = <?= json_encode($dispose_product); ?>;
    const group3Results = await Promise.all(group3Keys.map(key =>
      $.ajax({
        url: './ajax.pop_rack_content.php',
        method: 'POST',
        data: {
          wr_id,
          warehouse_key: key
        }
      })
    ));
    group3Results.forEach(html => $("#rack_container_03").append(html));
    $("#spinner3").hide();
  }

  $(document).on('keyup', '.rack_search', function() {
    if (event.keyCode === 13) {
      const k = $(this).val();
      const id = $(this).attr('id');
      const key = id.replace('_ser', '');
      const $rows = $('#' + key + '_list > tr');
      const tbodyElement = $('#' + key + '_list');

      if (k !== '') {
        const single_rack_result = $.ajax({
          url: './ajax.single_rack_search.php',
          method: 'POST',
          data: {
            wr_id,
            warehouse_key: key,
            rack_name: k
          },
          dataType: 'html'
        });

        single_rack_result.then(function(htmlContent) {
          tbodyElement.html(htmlContent);
        }).fail(function(jqXHR, textStatus, errorThrown) {
          console.error('Ajax 요청 실패:', textStatus, errorThrown);
          console.error('jqXHR 객체:', jqXHR);
          alert('데이터를 불러오는 데 실패했습니다. 다시 시도해 주세요.');
        });
      } else {
        const rack_results = $.ajax({
          url: './ajax.pop_rack_content.php',
          method: 'POST',
          data: {
            wr_id,
            warehouse_key: key,
            only_tr: true
          },
          dataType: 'html'
        });

        rack_results.then(function(htmlContent) {
          tbodyElement.html(htmlContent);
        }).fail(function(jqXHR, textStatus, errorThrown) {
          console.error('Ajax 요청 실패:', textStatus, errorThrown);
          console.error('jqXHR 객체:', jqXHR);
          alert('데이터를 불러오는 데 실패했습니다. 다시 시도해 주세요.');
        });
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('move2Frm');

    form.addEventListener('submit', function(event) {
      const itemNameInput = form.elements['ms_rack2'];
      const itemNameValue = itemNameInput.value.trim();
      
      if (itemNameValue === '') {
        alert('랙이름 입력 후 엔터를 눌러주십시오.');
        itemNameInput.focus();
        event.preventDefault();
        return;
      }

      if (!confirm('정말 재고 이동을 진행하시겠습니까?')) {
        alert('재고 이동이 취소되었습니다.');
        event.preventDefault();
        return;
      }

      console.log('폼이 정상적으로 제출됩니다.');
    });
  });
</script>

<?php
include_once(G5_PATH . '/tail.sub.php');
