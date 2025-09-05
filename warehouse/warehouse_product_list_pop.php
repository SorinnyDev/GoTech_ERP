<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_warehouse || !$date) {
  alert_close('잘못된 접근입니다.');
}

$where = '';
if ($sch_value) {
  $where .= " and wr_subject like '%$sch_value%' ";
}

$query = "
SELECT wr_product_id,
       wr_subject,
       wr_22 + 0 as 'price',
       CASE
           WHEN rs.wr_warehouse = '1000' THEN '한국창고'
           WHEN rs.wr_warehouse = '3000' THEN '미국창고'
           WHEN rs.wr_warehouse = '4000' THEN 'FBA창고'
           WHEN rs.wr_warehouse = '5000' THEN 'W-FBA창고'
           WHEN rs.wr_warehouse = '6000' THEN 'U-FBA창고'
           WHEN rs.wr_warehouse = '7000' THEN '한국반품창고'
           WHEN rs.wr_warehouse = '8000' THEN '미국반품창고'
           WHEN rs.wr_warehouse = '11000' THEN '한국폐기창고'
           WHEN rs.wr_warehouse = '12000' THEN '미국폐기창고'
           END               AS wr_warehouse_ko,
       rs.wr_rack,
       r.gc_name,
       rs.wr_datetime,
       SUM(wr_stock)         AS total_stock,
       SUM(wr_stock) * wr_22 AS total_stock_price
FROM g5_rack_stock AS rs
         INNER JOIN g5_write_product AS p ON p.wr_id = rs.wr_product_id
         LEFT JOIN g5_rack AS r ON rs.wr_rack = r.seq
WHERE rs.wr_datetime <= '{$date}' and rs.wr_warehouse = '{$wr_warehouse}' {$where}
GROUP BY rs.wr_product_id
ORDER BY rs.wr_datetime desc, wr_subject, rs.wr_rack;
";
$result = sql_fetch_all($query);

$list = [];

foreach ($result as $item) {
  if (!$item['total_stock']) {
    continue;
  }

  $list[] = $item;
}

$total_stock = 0;
$total_stock_price = 0;
$total_stock_amount = 0;

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

      .sch_input {
          position: relative;
          float: left;
          width: 200px;
          height: 40px;
          padding: 0 40px 0 10px;
          border: 0;
      }

      #bo_sch {
          float: left;
          position: relative;
          border: 1px solid #e6e6e6;
      }

      #bo_sch .sch_btn {
          position: absolute;
          width: 40px;
          height: 38px;
          margin-left: -40px;
          border: 0;
          background: none;
          font-size: 15px;
          color: #000;
      }
  </style>
  <div class="new_win">
  <h1><strong><?= current($result)['wr_warehouse_ko'] ?></strong> <?= $date . ' 기준재고 ' ?></h1>

  <form name="frm" method="post">
    <div id="excelfile_upload" class="result_list" style="padding:12px;">

      <div style="display: flex; justify-content: end; margin-bottom: 10px;">
        <fieldset id="bo_sch">
          <form name="fsearch" method="get">
            <input name="sch_value" value="" placeholder="상품명/SKU" id="sch_value" class="sch_input">
            <button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i> <span class="sound_only">검색</span></button>
          </form>
        </fieldset>
      </div>

      <div style="clear:both"></div>
      <div class="tbl_head01 tbl_wrap">
        <table>
          <thead style="position:sticky;top:0;">

          <tr>
            <th>상품명/SKU</th>
            <th style="width: 100px;">가격</th>
            <th>재고</th>
            <th style="width: 100px;">재고 금액</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($list as $k => $row) {
            $bg = 'bg' . ($k % 2);
            $total_stock += $row['total_stock'];
            $total_stock_price += $row['price'];
            $total_stock_amount += $row['total_stock_price'];
            ?>

            <tr class="<?php echo $bg; ?>">
              <td><?php echo $row['wr_subject'] ?></td>
              <td><?php echo number_format($row['price']) ?></td>
              <td><?php echo $row['total_stock'] ?></td>
              <td><?php echo number_format($row['total_stock_price']) ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td colspan="2"></td>
            <td><?= number_format($total_stock) ?></td>
            <td><?= number_format($total_stock_amount) ?></td>
          </tr>
          </tbody>

        </table>
      </div>

    </div>
  </form>

  <script>
  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');
