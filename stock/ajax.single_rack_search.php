<?php
include_once './_common.php';

if (!isset($wr_id) || !isset($warehouse_key) || !isset($rack_name)) alert('잘못된 접근입니다.');
$sql = "SELECT
          gr.seq,
          gr.gc_name,
          gr.gc_warehouse,
          gr.gc_use,
          COALESCE((
              SELECT SUM(wr_stock)
              FROM g5_rack_stock
              WHERE wr_rack = gr.seq -- 메인 쿼리의 랙 ID 사용
                AND wr_warehouse = gr.gc_warehouse
                AND wr_product_id = '{$wr_id}'
          ), 0) AS total_stock,
          COALESCE((
              SELECT SUM(A.wr_ea)
              FROM g5_sales2_list A
              LEFT JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num
              WHERE A.wr_rack = gr.seq -- 메인 쿼리의 랙 ID 사용
                AND A.wr_warehouse = gr.gc_warehouse
                AND A.wr_product_id = '{$wr_id}'
                AND A.wr_direct_use = '1'
                AND A.wr_etc_use = '0'
                AND IFNULL(B.wr_release_use, 0) = '0'
          ), 0) AS sales_stock
        FROM
            g5_rack gr
        WHERE
            gr.gc_name = '{$rack_name}'
            AND gr.gc_warehouse = '{$warehouse_key}'
            AND gr.gc_use = 1;";

$row = sql_fetch($sql);

if (!$row) {
?>
  <tr>
    <td colspan="2" style="height: 46px;">랙 없음</td>
  </tr>
<?php } else { ?>
  <tr>
    <td><?= $row['gc_name'] ?></td>
    <td>
      <form name="frmIndi<?= $rack_seq ?>" action="./rack_move_update.php" method="post">
        <input type="hidden" name="mode" value="indi">
        <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
        <input type="hidden" name="warehouse" value="<?= $warehouse_key ?>">
        <input type="hidden" name="seq" value="<?= $row['seq'] ?>">
        <input type="hidden" name="ori_qty" value="<?= $row['total_stock'] ?>">
        <input type="hidden" name="sales_stock" value="<?= $row['sales_stock'] ?>">
        <?= $row['total_stock'] ?> /
        <input type="number" name="qty" class="frm_input qty" value="<?= $row['total_stock'] + $row['sales_stock'] ?>" style="text-align:right;width:40%"
          oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
        <button class="btn01">수정</button>
      </form>
    </td>
  </tr>  
<?php } ?>