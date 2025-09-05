<?php
include_once('../common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);


//랙별 열 번호
$pattern = '/[A-Za-z]\d*/';
$rack = array();
$sql = "select * from g5_rack where gc_warehouse = 1000";
$rst = sql_query($sql);
for ($i = 0; $row = sql_fetch_array($rst); $i++) {

  $result = preg_replace($pattern, '', $row['gc_name']);

  if (!$result) $result = "기타";
  $rack[$result] .= $row['seq'] . "|";

}
ksort($rack);
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

      .tbl_head01 tbody tr:nth-child(even) {
          background: 0
      }

      @media (max-width: 767px) {
          .result_list {
              width: 100% !important
          }
      }
  </style>
  <div class="new_win">
    <h1>창고별 랙별 재고현황</h1>


    <div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">

      <table style="width:300px;border:0">
        <tr>
          <td>
            <form name="search">
              <div style="display: flex; gap: 5px;">
                <select name="sch_warehouse">
                  <?php foreach (PLATFORM_TYPE as $warehouse => $warehouse_name) { ?>
                    <option value="<?= $warehouse ?>" <?= get_selected($sch_warehouse, $warehouse) ?> ><?= $warehouse_name ?>창고</option>
                  <?php } ?>
                </select>
                <select name="rack" style="width:100%">
                  <option value="">선택하세요</option>
                  <?php foreach ($rack as $v => $k) {
                    echo '<option value="' . $v . '" ' . get_selected($_GET['rack'], $v) . '>' . $v . '열</option>';
                  } ?>
                </select>
                <select name="sch_stock">
                  <option value="">재고 전체</option>
                  <option value="Y" <?= get_selected('Y', $sch_stock) ?>>재고 있음</option>
                </select>
                <button type="submit" class="btn02" style="height: 35px; width: 200px;">검색</button>
                <button type="button" class="btn01" onclick="window.print()" style="height: 35px; width: 200px;">출력</button>
              </div>
            </form>
          </td>
        </tr>
      </table>

      <div style="clear:both"></div>


      <div class="tbl_head01 tbl_wrap" style="width:100%;">
        <table>
          <thead style="position:sticky;top:0;">

          <tr>
            <th style="width:10%">랙 번호</th>
            <th style="width:10%">총 재고</th>
            <th style="width:*">재고상품 현황</th>
          </tr>
          </thead>
          <tbody id="<?= $key ?>_list">
          <?php

          $sel_rack = explode('|', $rack[$_GET['rack']]);
          array_pop($sel_rack);
          $sel_rack_sql = implode(',', $sel_rack);

          $sql = "select * from g5_rack where seq IN({$sel_rack_sql}) order by gc_name asc";
          $rst = sql_query($sql);
          for ($i = 0; $row = sql_fetch_array($rst); $i++) {

            $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$row['gc_warehouse']}' and wr_rack = '{$row['seq']}' ");

            if ($sch_stock === 'Y' && !$stock['total']) {
              continue;
            }
            ?>
            <tr class="<?php echo $bg; ?>" style="border-bottom:2px solid #000">
              <td><?php echo $row['gc_name'] ?></td>
              <td>
                <?php echo number_format((int)$stock['total']) ?>개
              </td>
              <td style="height:200px;vertical-align:top">
                <?php //if($stock['total'] > 0) {?>
                <div class="tbl_head02 tbl_wrap" style="width:100%;">
                  <table>
                    <tr>
                      <th style="width:55">상품명</th>
                      <th style="width:15%">SKU</th>
                      <th style="width:15%">출고미완료</th>
                      <th style="width:15%">재고</th>
                      <th style="width:15%">총재고</th>
                      <th style="width:15%">실사재고</th>
                    </tr>
                    <?php
                    $sql2 = "SELECT a.*, b.wr_subject, b.wr_1, SUM(a.wr_stock) AS total_stock FROM g5_rack_stock a LEFT JOIN g5_write_product b ON(a.wr_product_id = b.wr_id) WHERE a.wr_rack = '{$row['seq']}' GROUP BY a.wr_product_id ;";

                    $rst2 = sql_query($sql2);
                    for ($a = 0; $row2 = sql_fetch_array($rst2); $a++) {

                      if ($sch_stock === 'Y' && !$row2['total_stock']) {
                        continue;
                      }

                      if (!$row2['wr_subject']) $row2['wr_subject'] = "제품삭제됨";
                      $sql = "SELECT SUM(A.wr_ea) AS sales_cnt FROM g5_sales2_list A
LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num
WHERE A.wr_etc_use = '0' AND A.wr_direct_use='1' AND IFNULL(B.wr_release_use,'0') = '0' AND A.wr_product_id='" . $row2['wr_product_id'] . "' AND A.wr_rack='" . $row2['wr_rack'] . "' AND A.wr_warehouse='" . $row2['wr_warehouse'] . "'";
                      $salesData = sql_fetch($sql);
                      ?>
                      <tr>
                        <td style="text-align:left"><?php echo $row2['wr_subject'] ?></td>
                        <td style="text-align:left"><?php echo $row2['wr_1'] ?></td>
                        <td style="text-align:right"><?= (int)$salesData['sales_cnt'] ?>개</td>
                        <td style="text-align:right"><?php echo $row2['total_stock'] ?>개</td>
                        <td style="text-align:right"><?php echo (int)$row2['total_stock'] + (int)$salesData['sales_cnt'] ?>개</td>
                        <td style="text-align:right"></td>
                      </tr>
                    <?php } ?>
                  </table>
                </div>
                <?php //}?>
              </td>
            </tr>
            <?php
          }
          ?>
          </tbody>
        </table>
      </div>

      <div style="clear:both"></div>
    </div>


    <div style="clear:both;margin-bottom:100px"></div>

  </div>

  <script>

    $(document).on('change', '[name=sch_warehouse]', function (e) {
      const warehouse = e.target.value;
      find_rack(warehouse);
    });

    function find_rack(warehouse = '1000') {

      $.post('./ajax.find_stock_rack.php', {warehouse}, function (result) {
        if (result.data) {
          const selectElement = document.querySelector('[name=rack]');
          const options = selectElement.querySelectorAll('option');
          for (let i = 1; i < options.length; i++) {
            options[i].remove();
          }

          for (const k in result.data) {
            const option = document.createElement('option');
            option.value = k;
            option.textContent = k + '열';
            selectElement.appendChild(option);
          }
        }
      }, 'json');
    }
  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');